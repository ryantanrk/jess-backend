<?php
    require_once '../connection.php';
    
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");

    //get filename
    $documentID = "";
    $title = "";
    $file = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $documentID = $_POST['documentID'];
        //get title & file
        $query = "SELECT `title`, `file` FROM `$documentTable` WHERE documentID = ?";
        $result = sqlProcesses($query, "s", [$documentID]);

        while ($row = mysqli_fetch_assoc($result)) {
            $title = $row['title'];
            $file = $row['file'];
        }

        //set headers
        header("Content-Type: application/pdf");
        header('Content-Disposition: inline; filename="' . $title . '.pdf"');
        header("Content-Transfer-Encoding: binary");
        header('Accept-Ranges: bytes');

        //determine filename
        $filename = $title . ".pdf";

        //create file and write to it
        $myfile = fopen("../documents/" . $filename, "w") or die("Unable to open file!");
        fwrite($myfile, $file);
        fclose($myfile);

        //go to file
        header('Location: ' . "../documents/" . $filename, true, 302);
    }
    else {
        echo json_encode([1 => "send post request"], JSON_PRETTY_PRINT);
    }

        // //get title & file
        // $query = "SELECT `title`, `file` FROM `$documentTable` WHERE documentID = ?";
        // $result = sqlProcesses($query, "s", [$documentID]);

        // while ($row = mysqli_fetch_assoc($result)) {
        //     $title = $row['title'];
        //     $file = $row['file'];
        // }

        // header("Content-Type: application/pdf");
        // header('Content-Disposition: inline; filename="' . $title . '.pdf"');
        // header("Content-Transfer-Encoding: binary");
        // header('Accept-Ranges: bytes');

        // $filename = $title . ".pdf";

        // $myfile = fopen("../documents/" . $filename, "w") or die("Unable to open file!");
        // fwrite($myfile, $file);
        // fclose($myfile);

        // header('Location: '. "../documents/" . $filename, true, 302);
?>