<?php
    require_once '../../connection.php';

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

    //get received input
    if ($access == 1) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //get attributes
            $type = $_POST['type'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $dob = $_POST['dob'];

            $personID = getNewID($type);
    
            //insert query
            $query = "INSERT INTO `$personTable` VALUES ($personID, $type, $username, $password, $email, $dob)";
    
            //if query is successful, echo success message, if not echo error
            if ($connection->query($query) === TRUE) {
                echo "New person record created successfully";
            }
            else {
                echo "Error when creating person record: " . $query . "<br>" . $connection->error;
            }

            //if reviewer
            if ($type == 2) {
                $areaOfExpertise = $_POST['areaOfExpertise'];
                $status = "available";
                $queryr = "INSERT INTO `$reviewerTable` VALUES ($personID, $areaOfExpertise, $status)";

                if ($connection->query($query) === TRUE) {
                    echo "New reviewer record created successfully";
                }
                else {
                    echo "Error when creating reviewer record: " . $query . "<br>" . $connection->error;
                }
            }
    
            $connection->close(); //close connection
        }
        else {
            echo "No data received";
        }
    }
    else {
        echo "Access denied.";
    }
?>