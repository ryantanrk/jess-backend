<?php
    require_once '../connection.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");

    $arr = [1 => "Send a POST request to this url!"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //get email
        $email = $_POST['emailAddress'];
        //get new password
        $newPassword = md5($_POST['newPassword']);

        //check email for existing account
        $query = "UPDATE `$personTable` SET `password` = '$newPassword' WHERE `email` = '$email'";

        if ($connection->query($query) === TRUE) {
            //if email is valid
            $arr = [
                "condition" => "success"
            ];
        }
        else {
            $arr = [
                "error" => $connection->error
            ];
        }
    }
    echo json_encode($arr, JSON_PRETTY_PRINT);
?>