<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");
    require_once '../../connection.php';

    $array = [
        "error" => "no post"
    ];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $recUsername = $_POST['username'];
        $recEmail = $_POST['email'];
        $recPass = md5($_POST['password']);
        $recDob = $_POST['dob'];
        $recType = $_POST['type'];
        $personID = getNewID($recType);
    
        $query = "INSERT INTO person (`personID`, `type`, `username`, `password`, `email`, `dob`)  VALUES ('$personID', '$recType', '$recUsername', '$recPass', '$recEmail', '$recDob')";

        if ($connection->query($query) === TRUE) {
            $array = [
                "username" => $recUsername,
                "condition" => "success"
            ];
        } else {
            $array = [
                "username" => $recUsername,
                "condition" => "failed",
                "error" => "Error: " . $query . "<br>" . $connection->error
            ];
        }
        $connection->close();
    }

    echo json_encode($array, JSON_PRETTY_PRINT);
?>