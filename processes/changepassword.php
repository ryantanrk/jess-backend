<?php
    require_once '../connection.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");

    $arr = [1 => "Send a POST request to this url!"];

    function changePassword($email, $newPassword) {
        global $arr;
        
        //check email for existing account
        $query = "UPDATE `person` SET `password` = ? WHERE `email` = ?";

        sqlProcesses($query, "ss", [md5($newPassword), $email]);

        $arr = [
            "condition" => "success change password for: " . $email
        ];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //get email
        $email = $_POST['emailAddress'];
        //get new password
        $newPassword = $_POST['newPassword'];
        changePassword($email, $newPassword);
    }
    echo json_encode($arr, JSON_PRETTY_PRINT);
?>