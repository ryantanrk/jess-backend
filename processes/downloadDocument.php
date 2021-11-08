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
    $arr = [1 => "send a POST request to this url!"];
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $documentID = $_POST['documentID'];
        //get title & file
        $query = "SELECT `title`, `file` FROM `$documentTable` WHERE documentID = ?";
        $result = sqlProcesses($query, "s", [$documentID]);

        //if result found
        if (mysqli_num_rows($result) > 0) {
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
            $arr = ["error" => "document not found"];
        }
    }
    else {
        echo $arr;
    }
?>