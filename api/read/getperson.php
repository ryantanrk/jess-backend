<?php
//url - localhost:80/api/read/getperson.php?api_key=&id=&type=
    require_once '../../connection.php';
    require_once '../../class/person.php';
    require_once '../../factory/personfactory.php';

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
        $conditions[] = " WHERE personID = `$id` ";
    }

    //get from person type
    $type = "";
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
        $conditions[] = " WHERE type = `$type` ";
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

        if (!empty($conditions)) {
            $query .= implode(' AND ', $conditions);
        }

        echo "<script>console.log(" . $query . ")</script>"; //test

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            if ($row['type'] == 0) {
                $factoryobj = new EditorFactory;
                $editorobj = $factoryobj->createNewUser($row['personID'], $row['username'], $row['password'], $row['email'], $row['dob']);
                //$editorobj = new Editor($row['personID'], $row['username'], $row['password'], $row['email'], $row['dob']);

                array_push($personarray, $editorobj);
            }
            else if ($row['type'] == 1) {
                $factoryobj = new AuthorFactory;
                $authorobj = $factoryobj->createNewUser($row['personID'], $row['username'], $row['password'], $row['email'], $row['dob']);
                //$authorobj = new Author($row['personID'], $row['username'], $row['password'], $row['email'], $row['dob']);
                array_push($personarray, $authorobj);
            }
            else if ($row['type'] == 2) {
                $factoryobj = new ReviewerFactory;
                $reviewerobj = $factoryobj->createNewUser($row['personID'], $row['username'], $row['password'], $row['email'], $row['dob']);
                //$reviewerobj = new Reviewer($row['personID'], $row['username'], $row['password'], $row['email'], $row['dob']);
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