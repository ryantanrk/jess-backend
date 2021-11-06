<?php
    require_once '../connection.php';
    
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $documentID = $_POST['documentID'];

        $query = "SELECT file FROM `$documentTable` WHERE documentID = ?";
        $result = sqlProcesses($query, "s", [$documentID]);

        $file = "";
        while ($row = mysqli_fetch_assoc($result)) {
            $file = addslashes($row['file']);
        }
        echo $file;
    }
?>