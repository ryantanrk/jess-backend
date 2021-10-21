<?php
//url - localhost:80/api/read/getperson.php?api_key=
    require_once '../../connection.php';
    require_once '../../class/person.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $api_key = "";
    //get api key from url
    if (isset($_GET['api_key'])) {
        $api_key = $_GET['api_key'];
    }

    //get list of api keys
    $api_keys_file = file_get_contents('../api_keys.json'); //get from file
    $api_keys = json_decode($api_keys_file, true); //decode json
    $key_array = $api_keys['api_keys']; //array

    $access = 0; //var that grants access

    foreach ($key_array as $key) {
        if ($api_key === $key) {
            //if api key matches, grant access
            $access = 1;
        }
    }

    $personarray = [];

    if ($access == 1) {
        //if access granted
        $query = "SELECT * FROM `$personTable`"; //query
        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));

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
    }
    else {
        //if access denied
        array_push($personarray, "Access denied.");
    }

    echo json_encode($personarray, JSON_PRETTY_PRINT);
?>