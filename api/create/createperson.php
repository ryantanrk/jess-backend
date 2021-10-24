<?php
    require_once '../../connection.php';
    //get received input
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //get attributes
        $personID = $_POST['personID'];
        $type = $_POST['type'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $dob = $_POST['dob'];

        //insert query
        $query = "INSERT INTO `$personTable` VALUES ($personID, $type, $username, $password, $email, $dob)";

        //if query is successful, echo success message, if not echo error
        if ($connection->query($query) === TRUE) {
            echo "New record created successfully";
        }
        else {
            echo "Error: " . $query . "<br>" . $connection->error;
        }

        $connection->close(); //close connection
    }
    else {
        echo "No object received";
    }

    // require_once '../../connection.php';

    // if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //     $text = $_POST['text'];
    //     $query = "INSERT INTO react_php (`id`, `text`)  VALUES (NULL, '$text')";

    //     if ($connection->query($query) === TRUE) {
    //         echo "New record created successfully";
    //     } else {
    //         echo "Error: " . $query . "<br>" . $connection->error;
    //     }
    //     $connection->close();
    // }
?>