<?php
//url - localhost:80/api/read/getreview.php?api_key=(api_key)&id=(id)&docID=(type)&reviewerID=(search)
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

    //get single id
    $id = "";
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $conditions[] = " reviewID = '$id' ";
    }

    //get from documentID
    $docID = "";
    if (isset($_GET['docID'])) {
        $docID = $_GET['docID'];
        $conditions[] = " documentID = '$docID' ";
    }

    //get from reviewerID
    $reviewerID = "";
    if (isset($_GET['reviewerID'])) {
        $reviewerID = $_GET['reviewerID'];
        $conditions[] = " reviewerID = '$reviewerID' ";
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
            //get row, make document review struct and push the struct to the review array
        }
    }
    else {
        //if access denied
        array_push($reviewarray, "Access denied.");
    }

    echo json_encode($reviewarray);
?>