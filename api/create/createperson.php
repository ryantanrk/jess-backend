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

    //function to get a new ID (for any table)
    function getNewID($type) : string {
        require_once '../../connection.php';
        
        //get prefix & query
        $prefix = "";
        $query = "";
        switch ($type) {
            case 0: //person
                $prefix = "P";
                $query = "SELECT 'personID' FROM `$personTable`";
                break;
            case 1: //reviewer
                $prefix = "R";
                $query = "SELECT 'reviewerID' FROM `$reviewerTable`";
                break;
            case 2: //document
                $prefix = "D";
                $query = "SELECT 'documentID' FROM `$documentTable`";
                break;
            case 3: //review
                $prefix = "E";
                $query = "SELECT 'reviewID' FROM `$reviewTable`";
                break;
        }

        $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
        
        $chosenID = $prefix + "0001";
        if ($row->count()) {
            //if exists
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $id = substr($row['personID'], 1);
                if ($id > substr($chosenID, 1)) {
                    //check digits length
                    $length = strlen((string)$id);
                    if ($length == 1) {
                        $chosenID = $prefix + "000" + $id;
                    }
                    else if ($length == 2) {
                        $chosenID = $prefix + "00" + $id;
                    }
                    else if ($length == 3) {
                        $chosenID = $prefix + "0" + $id;
                    }
                    else if ($length == 4) {
                        $chosenID = $prefix + $id;
                    }
                }
            }
        }

        return $chosenID;
    }

    //get received input
    if ($access == 1) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //get attributes
            $personID = getNewID(0);
            $type = $_POST['type'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $dob = $_POST['dob'];
    
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
                $reviewerID = getNewID(1);
                $status = "available";
                $queryr = "INSERT INTO `$reviewerTable` VALUES ($reviewerID, $personID, $status)";

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