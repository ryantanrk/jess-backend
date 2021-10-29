<?php
//url - localhost:80/api/read/getdocument.php?api_key=(api_key)&id=(id)&type=(type)&search=(search)
//mandatory attribute: ?api_key
//optional: id, type, search
    require_once '../../connection.php';
    require_once '../../class/document.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $api_key = "";
    //get api key from url
    if (isset($_GET['api_key'])) {
        $api_key = $_GET['api_key'];
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

            $type = $row['type'];

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

            //content
            $file = $row['file'];

            //content array
            $content = array(
                "pdfFile" => $file
            );

            $documentobj = "";

            //check type
            if ($type == 0) {
                $documentobj = new Document(new ManuscriptState);
            }
            else if ($type == 1) {
                $documentobj = new Document(new JournalState);
            }
            
            $metares = $documentobj->setDocumentMetaData($metadata); //set metadata
            $contentres = $documentobj->documentState->setDocumentContent($content); //set content

            array_push($docarray, $documentobj);
        }
    }
    else {
        array_push($docarray, "Access denied.");
    }

    echo json_encode($docarray, JSON_PRETTY_PRINT);
?>