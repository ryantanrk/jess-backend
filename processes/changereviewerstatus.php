<?php
    require_once '../connection.php';
    require_once '../class/person.php';
    require_once '../class/factory.php';
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");
    
    $arr = ["1" => "Send a POST request to this url!"];

    function changeReviewerStatus($reviewerID, $status) {
        global $arr;
        $reviewer = getPersonFromID($reviewerID);
        $reviewer->setReviewerStatus($status);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $reviewerID = $_POST['reviewerID'];
        $status = $_POST['status'];

        changeReviewerStatus($reviewerID, $status);
    }

    changeReviewerStatus("R1", "available");

    echo json_encode($arr, JSON_PRETTY_PRINT);
?>