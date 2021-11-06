<?php
//url - api/read/getdocument.php?api_key=(api_key)&authorID=(authorID)&docStatus=(docStatus)&docID=(docID)
//mandatory attribute: ?api_key
//optional: authorID, docStatus, docID
//docstatus can be split using (status1),(status2)
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
        $statusq_array = explode(",", $docStatus);

        $status_condition = "";
        if (count($statusq_array) == 1) {
            //if only 1 status
            $status_condition = " documentStatus = '$docStatus' ";
        }
        else {
            $status_condition = " documentStatus = '$statusq_array[0]' ";
            for ($i = 1; $i < count($statusq_array); $i++) {
                $status_condition .= " OR documentStatus = '$statusq_array[$i]' ";
            }
        }

        $conditions[] = $status_condition;
    }

    $docID = "";
    if (isset($_GET['docID'])) {
        $docID = $_GET['docID'];
        $conditions[] = " documentID = '$docID' ";
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
        $query = "SELECT * FROM `$documentTable` ";

        if (!empty($conditions)) {
            $query .= ' WHERE ';
            $query .= implode(' AND ', $conditions);
        }

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            //get data
            $documentID = $row['documentID'];
            $authorID = $row['authorID'];
            $status = $row['documentStatus'];

            //get author username
            $queryUser = "SELECT username FROM `$personTable` WHERE `personID` = ?";
            $resultUser = sqlProcesses($queryUser, "s", [$authorID]);

            $authorUsername = "";
            while ($rowUser = mysqli_fetch_array($resultUser, MYSQLI_ASSOC)) {
                $authorUsername = $rowUser['username'];
            }

            $metadata_arr = [
                "documentID" => $documentID,
                "authorID" => $authorID,
                "authorUsername" => $authorUsername,
                "editorID" => $row['editorID'],
                "title" => $row['title'],
                "topic" => $row['topic'],
                "dateOfSubmission" => $row['dateOfSubmission'],
                "authorRemarks" => $row['authorRemarks'],
                "editorRemarks" => $row['editorRemarks'],
                "reviewDueDate" => $row['reviewDueDate'],
                "editDueDate" => $row['editDueDate'],
                "price" => $row['price'],
                "documentStatus" => $row['documentStatus'],
                "printDate" => $row['printDate'],
                "journalIssue" => $row['journalIssue']
            ];

            $documentobj = "";
            if ($row['documentStatus'] != "Published") {
                $documentobj = new Document(new ManuscriptState);
                $metadata_arr['printDate'] = "0000-00-00";
                $metadata_arr['journalIssue'] = "0";
            }
            else {
                $documentobj = new Document(new JournalState);
            }
            
            //document metadata
            $metadata = new DocumentMetadata($metadata_arr);
            foreach ($metadata as $key => $value) {
                $documentobj->documentStateObject->setDocumentMetaData($key, $value);
            }
            
            //set reviews
            $query = "SELECT * FROM `$reviewTable` WHERE `documentID` = ?";
            $paramVariablesArray = [$documentID];
            $resultR = sqlProcesses($query, "s", $paramVariablesArray);

            $i = 0;
            while ($rowR = mysqli_fetch_array($resultR, MYSQLI_ASSOC)) {
                //get review object
                $review = new DocumentReview($rowR);
                
                //set review
                $documentobj->DocumentReviewsArray[$i] = $review;
                $i++;
            }

            array_push($docarray, $documentobj);
        }
    }
    else {
        array_push($docarray, "Access denied.");
    }

    echo json_encode($docarray, JSON_PRETTY_PRINT);
?>