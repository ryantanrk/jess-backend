<?php
    require_once '../connection.php';
    require_once '../class/document.php';
    
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");

    //get filename
    $documentID = "";
    $title = "";
    $file = "";
    $arr = [1 => "Send a POST request to this url!"];

    function downloadDocument($documentID) {
        $document = retrieveDocumentFromDatabaseInCorrectState($documentID);
        $document->getDocumentContent();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $documentID = $_POST['documentID'];
        downloadDocument($documentID);
    }
    else {
        echo json_encode($arr, JSON_PRETTY_PRINT);
    }
?>