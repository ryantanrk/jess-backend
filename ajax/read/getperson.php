<?php
//url - localhost:80/ajax/read/getperson.php
    require_once '../../connection.php';
    require_once '../../class/editor.php';
    require_once '../../class/author.php';
    require_once '../../class/reviewer.php';
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    //query
    $query = "SELECT * FROM `$personTable`";
    $result = mysqli_query($connection, $query) or die(mysqli_error($connection));

    $personarray = [];

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        if ($row['type'] == 0) {
            $editorobj = new Editor($row['personID'], $row['username'], $row['password'], $row['email'], $row['dob']);
            array_push($personarray, $editorobj);
        }
        else if ($row['type'] == 1) {
            $authorobj = new Author($row['personID'], $row['username'], $row['password'], $row['email'], $row['dob']);
            array_push($personarray, $authorobj);
        }
        else if ($row['type'] == 2) {
            $reviewerobj = new Reviewer($row['personID'], $row['username'], $row['password'], $row['email'], $row['dob']);
            array_push($personarray, $reviewerobj);
        }
    }

    echo json_encode($personarray, JSON_PRETTY_PRINT);
?>