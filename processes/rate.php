<?php
    require_once '../connection.php';
    require_once '../factory/personfactory.php';
    require_once '../class/person.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");
    
    $arr = ["1" => "Send a POST request to this url!"];

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $documentID = $_POST['documentID'];
        $reviewerID = $_POST['personID'];
        $rating = $_POST['rating'];
        $comment = $_POST['comment'];

        $revfactory = new ReviewerFactory;
        $reviewerobj = $revfactory->getNewUser($reviewerID);

        //for updating review
        $review_arr = [
            "documentID" => $documentID,
            "reviewerID" => $reviewerID,
            "rating" => $rating,
            "comment" => $comment,
            "reviewStatus" => "complete",
            "dateOfReviewCompletion" => date("Y-m-d")
        ];

        $reviewobj = new DocumentReview($review_arr);

        //check document and rating
        $result = $reviewerobj->rate($reviewobj);
    }

    echo json_encode($arr, JSON_PRETTY_PRINT);
?>