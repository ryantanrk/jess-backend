<?php
    require_once '../connection.php';
    require_once '../class/person.php';
    require_once '../class/factory.php';
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");
    
    $arr = ["1" => "Send a POST request to this url!"];

    function approveReviewer($editorID, $reviewerID) {
        $editor = getPersonFromID($editorID);
        $editor->approveReviewer($reviewerID);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $editorID = $_POST['editorID'];
        $reviewerID = $_POST['reviewerID'];

        approveReviewer($editorID, $reviewerID);
    }
    
    echo json_encode($arr, JSON_PRETTY_PRINT);
?>