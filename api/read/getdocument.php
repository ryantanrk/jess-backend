<?php
//url - api/read/getdocument.php?api_key=(api_key)&authorID=(authorID)&docStatus=(docStatus)
//&docID=(docID)&reviewerID=(reviewerID)&reviewStatus=(reviewStatus)
//mandatory attribute: ?api_key
//optional: authorID, docStatus, docID, reviewerID, reviewStatus
    require_once '../../connection.php';
    require_once '../../class/document.php';
    require_once '../../class/review.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

    $api_key = "";
    //get api key from url
    if (isset($_GET['api_key'])) {
        $api_key = $_GET['api_key'];
    }

    //conditions
    $conditions = [];
    
    $author_ID = "";
    if (isset($_GET['authorID'])) {
        $author_ID = $_GET['authorID'];
        $conditions[] = " authorID = '$author_ID' ";
    }

    $docStatus = "";
    if (isset($_GET['docStatus'])) {
        $docStatus = $_GET['docStatus'];
        $conditions[] = " D.status = '$docStatus' ";
    }

    $docID = "";
    if (isset($_GET['docID'])) {
        $docID = $_GET['docID'];
        $conditions[] = " R.documentID = '$docID' ";
    }

    $reviewerID = "";
    if (isset($_GET['reviewerID'])) {
        $reviewerID = $_GET['reviewerID'];
        $conditions[] = " R.reviewerID = '$reviewerID' ";
    }

    $reviewStatus = "";
    if (isset($_GET['reviewStatus'])) {
        $reviewStatus = $_GET['reviewStatus'];
        $conditions[] = " R.status = '$reviewStatus' ";
    }

    //get list of api keys
    $api_keys_file = file_get_contents('../api_keys.json'); //get from file
    $api_keys = json_decode($api_keys_file, true); //decode json
    $key_array = $api_keys['api_keys']; //array

    $access = 0; //var that grants access

    foreach ($key_array as $key) {
        if ($api_key === $key) {
            //if api key matches, grant access
            $access = 1;
        }
    }

    $docarray = [];
    if ($access == 1) {
        $query = "SELECT * FROM `$documentTable` AS D
                LEFT OUTER JOIN `$reviewTable` AS R ON D.documentID = R.documentID ";

        if (!empty($conditions)) {
            $query .= ' WHERE ';
            $query .= implode(' AND ', $conditions);
        }

        $query .= " GROUP BY D.documentID ";

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            //get data
            $documentID = $row['documentID'];
            $authorID = $row['authorID'];
            $title = $row['title'];
            $topic = $row['topic'];
            $pages = $row['pages'];
            $dateOfSubmission = $row['dateOfSubmission'];
            $authorRemarks = $row['authorRemarks'];
            $editorRemarks = $row['editorRemarks'];
            $status = $row['status'];
            $price = $row['price'];

            //get author username
            $queryUser = "SELECT username FROM `$personTable` WHERE `personID` = ?";
            $resultUser = sqlProcesses($queryUser, "s", [$authorID]);

            $authorUsername = "";
            while ($rowUser = mysqli_fetch_array($resultUser, MYSQLI_ASSOC)) {
                $authorUsername = $rowUser['username'];
            }

            //metadata array
            $metadata = [
                "documentID" => $documentID,
                "authorID" => $authorID,
                "username" => $authorUsername,
                "title" => $title,
                "topic" => $topic,
                "pages" => $pages,
                "dateOfSubmission" => $dateOfSubmission,
                "authorRemarks" => $authorRemarks,
                "editorRemarks" => $editorRemarks,
                "status" => $status,
                "price" => $price
            ];

            //content
            $file = $row['file'];

            //content array
            $content = array(
                "pdfFile" => $file
            );

            $documentobj = "";

            //check type
            if ($status != "Published") {
                $documentobj = new Document(new ManuscriptState);
            }
            else {
                $documentobj = new Document(new JournalState);
            }
            
            $metares = $documentobj->setDocumentMetaData($metadata); //set metadata
            //$contentres = $documentobj->documentState->setDocumentContent($content); //set content
            //set reviews
            $query = "SELECT * FROM `$reviewTable` WHERE `documentID` = ?";
            $paramVariablesArray = [$documentID];
            $resultR = sqlProcesses($query, "s", $paramVariablesArray);

            while ($rowR = mysqli_fetch_array($resultR, MYSQLI_ASSOC)) {
                //get review object
                $reviewobj = new Review($rowR['reviewerID'], $rowR['documentID']);
                $reviewobj->setReview($rowR['rating'], $rowR['comment']);
                $reviewobj->status = $rowR['status'];
                $reviewobj->dueDate = $rowR['dueDate'];
                $documentobj->setDocumentReviews($reviewobj);
            }

            array_push($docarray, $documentobj);
        }
    }
    else {
        array_push($docarray, "Access denied.");
    }

    echo json_encode($docarray, JSON_PRETTY_PRINT);
?>