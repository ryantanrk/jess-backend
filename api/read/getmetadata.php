<?php
//url - /api/read/getmetadata.php?api_key=(api_key)&authorID=(authorID)
//mandatory attribute: ?api_key
//optional: authorID
    require_once '../../connection.php';
    require_once '../../class/document.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $api_key = "";
    //get api key from url
    if (isset($_GET['api_key'])) {
        $api_key = $_GET['api_key'];
    }

    //sql conditions array
    $conditions = [];

    $authorID = "";
    if (isset($_GET['authorID'])) {
        $authorID = $_GET['authorID'];
        $conditions[] = " authorID = '$authorID' ";
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
        $query = "SELECT * FROM `$documentTable`";

        if (!empty($conditions)) {
            $query .= ' WHERE ';
            $query .= implode(' AND ', $conditions);
        }

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

            //metadata array
            $metadata = array(
                "documentID" => $documentID,
                "authorID" => $authorID, 
                "title" => $title,
                "topic" => $topic,
                "pages" => $pages,
                "dateOfSubmission" => $dateOfSubmission,
                "authorRemarks" => $authorRemarks,
                "editorRemarks" => $editorRemarks,
                "status" => $status
            );
            
            array_push($docarray, $metadata);
        }
    }
    else {
        array_push($docarray, "Access denied.");
    }

    echo json_encode($docarray, JSON_PRETTY_PRINT);
?>