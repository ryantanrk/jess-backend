<?php
//url - api/read/getreview.php?api_key=(api_key)&id=(id)&docID=(type)&reviewerID=(search)
//mandatory: ?api_key
//optional: id, docID, reviewerID
    require_once '../../connection.php';
    require_once '../../class/review.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $api_key = "";
    //get api key from url
    if (isset($_GET['api_key'])) {
        $api_key = $_GET['api_key'];
    }

    //sql conditions array
    $conditions = [];
    $paramArray = [];

    //get single id
    $id = "";
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $conditions[] = " reviewID = '$id' ";
        $paramArray[] = $id;
    }

    //get from documentID
    $docID = "";
    if (isset($_GET['docID'])) {
        $docID = $_GET['docID'];
        $conditions[] = " documentID = '$docID' ";
        $paramArray[] = $docID;
    }

    //get from reviewerID
    $reviewerID = "";
    if (isset($_GET['reviewerID'])) {
        $reviewerID = $_GET['reviewerID'];
        $conditions[] = " reviewerID = '$reviewerID' ";
        $paramArray[] = $reviewerID;
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

    $reviewarray = [];

    if ($access == 1) {
        //if access granted
        $query = "SELECT * FROM `$reviewTable`"; //query

        if (!empty($conditions)) {
            $query .= ' WHERE ';
            $query .= implode(' AND ', $conditions);
        }

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            //review object
            $reviewobj = new Review($row['reviewerID'], $row['documentID'], $row['rating'], $row['comment']);

            array_push($reviewarray, $reviewobj);
        }
    }
    else {
        //if access denied
        array_push($reviewarray, "Access denied.");
    }

    echo json_encode($reviewarray, JSON_PRETTY_PRINT);
?>