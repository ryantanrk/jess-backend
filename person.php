<?php
//localhost/person.php
    require_once 'connection.php';
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    //query
    $query = "SELECT * FROM `$personTable`";

    //result parse to json
    $result = mysqli_query($connection, $query) or die(mysqli_error($connection));

    $return_arr = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $row_arr['personID'] = $row['personID'];
        $row_arr['username'] = $row['username'];
        $row_arr['email'] = $row['email'];

        array_push($return_arr, $row_arr);
    }
    echo json_encode($return_arr);
?>