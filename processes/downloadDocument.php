<?php
    require_once '../connection.php';
    
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $documentID = $_POST['documentID'];

        $query = "SELECT title, file FROM `$documentTable` WHERE documentID = ?";
        $result = sqlProcesses($query, "s", [$documentID]);

        $file = "";
        while ($row = mysqli_fetch_assoc($result)) {
            $file = $row['file'];
        }
        echo $file;
    }
    else {
        echo json_encode([1 => "send post request"], JSON_PRETTY_PRINT);
    }
?>