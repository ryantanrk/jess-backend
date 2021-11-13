<?php
//url - api/read/getperson.php?api_key=(api_key)&id=(id)&type=(type)&search=(search)&status=(status)
//&expertise=(expertise)
//mandatory: ?api_key
//optional: id, type, search, status, expertise
    require_once '../../connection.php';
    require_once '../../class/person.php';
    require_once '../../class/factory.php';

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
        $conditions[] = " P.personID = '$id' ";
    }

    //get from person type
    $type = "";
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
        $conditions[] = " P.type = '$type' ";
    }

    //get from reviewer status
    $status = "";
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $statusq_array = explode(",", $status);

        $status_condition = "";
        if (count($statusq_array) == 1) {
            //if only 1 status
            $status_condition = " status = '$status' ";
        }
        else {
            $status_condition = " status = '$statusq_array[0]' ";
            for ($i = 1; $i < count($statusq_array); $i++) {
                $status_condition .= " OR status = '$statusq_array[$i]' ";
            }
        }

        $conditions[] = $status_condition;
    }

    //get from reviewer area of expertise
    $expertise = "";
    if (isset($_GET['expertise'])) {
        $expertise = $_GET['expertise'];
        $conditions[] = " R.areaOfExpertise = '$expertise' ";
    }

    //get from search term (personID, username, or email)
    $search = "";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $conditions[] = " (P.personID LIKE '%$search%'
        OR P.username LIKE '%$search%' 
        OR P.email LIKE '%$search%') ";
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
        //query
        $query = "SELECT P.personID, type FROM `person` AS P
        LEFT OUTER JOIN `reviewerspecific` AS R ON P.personID = R.personID ";

        if (!empty($conditions)) {
            $query .= ' WHERE ';
            $query .= implode(' AND ', $conditions);
        }

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $personID = $row['personID'];
                if ($row['type'] == 0) {
                    // $editorobj = getPersonFromID($personID);
                    // array_push($personarray, $editorobj);
                }
                else if ($row['type'] == 1) {
                    $authorobj = getPersonFromID($personID);
                    $person_arr = $authorobj->getPersonData();
                    array_push($personarray, $person_arr);
                }
                else if ($row['type'] == 2) {
                    $reviewerobj = getPersonFromID($personID);
                    $person_arr = $reviewerobj->getPersonData();
                    array_push($personarray, $person_arr);
                }
            }
        }
        else {
            $personarray = ["error" => "No person found."];
        }
    }
    else {
        //if access denied
        array_push($personarray, "Access denied.");
    }

    echo json_encode($personarray, JSON_PRETTY_PRINT);
?>