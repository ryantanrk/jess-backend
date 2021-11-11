<?php
    require_once '../connection.php';
    require_once '../class/person.php';
    require_once '../class/factory.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");
    
    $arr = ["1" => "Send a POST request to this url!"];

    function rate($documentID, $reviewerID, $rating, $comment) {
        global $arr;
        $reviewer = getPersonFromID($reviewerID);

        //for updating review
        $review_arr = [
            "rating" => $rating,
            "comment" => $comment,
            "reviewStatus" => "complete",
            "dateOfReviewCompletion" => date("Y-m-d")
        ];

        //get document
        $documentobj = new Document(new ManuscriptState, $documentID);
        //get metadata object
        $metadataobj = $documentobj->documentMetaDataObject;

        $ratings = []; //ratings array

        //iterate over review array
        if ($metadataobj->documentStatus === "under review") {
            if (count($documentobj->documentReviewsArray) > 0) {
                foreach ($documentobj->documentReviewsArray as $review) {
                    //if reviewer ID matches
                    if ($review->reviewerID === $reviewerID && $review->reviewStatus === "pending") {
                        //set review attributes
                        foreach ($review_arr as $key => $value) {
                            $reviewer->setAuthorizedDocumentAttribute($documentID, $key, $value);
                            if ($key === "rating") {
                                $ratings[] = $value; //add rating to ratings
                            }
                        }
                        //feedback array
                        $arr = [
                            "message" => "rate complete: " . $review->documentID . ", from " . $review->reviewerID,
                            "rating" => $review_arr['rating'],
                            "comment" => $review_arr['comment'],
                            "dateOfReviewCompletion" => $review_arr['dateOfReviewCompletion']
                        ];
                    }
                    else if ($review->reviewerID === $reviewerID && $review->reviewStatus === "complete") {
                        $arr = [
                            "error" => "document " . $documentID . " has already been reviewed by " . $reviewerID
                        ];
                        $ratings = []; //empty ratings to prevent status change
                    }
                    else if ($review->reviewerID != $reviewerID) {
                        //if another reviewer has been assigned the document
                        if ($review->reviewStatus === "complete") {
                            //if that review is complete, add existing score in ratings array
                            $ratings[] = $review->rating;
                        }
                    }
                    else {
                        $arr = [
                            "error" => "Reviewer " . $reviewerID . " is not authorized to review " . $documentID
                        ];
                        $ratings = []; //empty ratings to prevent status change
                    }
                }

                //if more than 2 completed ratings, change document status
                if (count($ratings) > 1) {
                    $newStatus = "rejected";
                    if (count($ratings) == 2) {
                        if (max($ratings) < 7) {
                            //if both ratings less than 7, reject
                            $newStatus = "rejected";
                        }
                        else if (min($ratings) >= 7) {
                            //if both ratings above 7, approve
                            $newStatus = "pending compile";
                        }
                        else if ($ratings[0] < 7 || $ratings[1] < 7) {
                            $newStatus = "pending additional reviewer";
                        }
                    }
                    else if (count($ratings) == 3) {
                        //if 3rd rating
                        if ($review_arr['rating'] >= 7) {
                            //3rd rating above 7, pending compile
                            $newStatus = "pending compile";
                        }
                        else {
                            //below 7, rejected
                            $newStatus = "rejected";
                        }
                    }
                    $metadataobj->setMetaData("documentStatus", $newStatus);
                    $arr["newStatus"] = $newStatus;
                }
            }
            else {
                //error when no existing review
                $arr = [
                    "error" => "Reviewer " . $reviewerID . " is not authorized to review " . $documentID
                ];
            }
        }
        else {
            $arr = [
                "error" => "document is not under review"
            ];
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $documentID = $_POST['documentID'];
        $reviewerID = $_POST['personID'];
        $rating = $_POST['rating'];
        $comment = $_POST['comment'];

        rate($documentID, $reviewerID, $rating, $comment);
    }

    echo json_encode($arr, JSON_PRETTY_PRINT);
?>