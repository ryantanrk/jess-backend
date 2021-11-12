<?php
    require_once '../connection.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");

    $arr = [1 => "i forgor :skull:"];
    
    function forgotPassword($email) {
        global $arr;
        $paramVariablesArray = [$email];

        //check email for existing account
        $query = "SELECT * FROM `person` WHERE `email` = ?";
        $result = sqlProcesses($query, "s", $paramVariablesArray);

        if (mysqli_num_rows($result) == 1) {
            //if email is valid
            $arr = [
                "email" => $email,
                "condition" => "success"
            ];
        }
        else {
            //if email is not valid
            $arr = [
                "condition" => "failed"
            ];
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //get email
        $email = $_POST['email'];
        forgotPassword($email);
    }
    echo json_encode($arr, JSON_PRETTY_PRINT);
?>