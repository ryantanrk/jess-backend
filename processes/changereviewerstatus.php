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
        $reviewerdata = $reviewer->getPersonData();

        if ($reviewerdata['status'] != $status) {
            //if doesn't match status
            $reviewer->setReviewerStatus($status);
            $arr = ["message" => "reviewer " . $reviewerID . " status changed to " . $status];
        }
        else {
            //if match status
            $arr = ["error" => "reviewer status is already the same"];
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $reviewerID = $_POST['reviewerID'];
        $status = $_POST['status'];

        changeReviewerStatus($reviewerID, $status);
    }

    echo json_encode($arr, JSON_PRETTY_PRINT);
?>