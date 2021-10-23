<?php
    //get received input
    $json = file_get_contents('php://input');
    //decode into array
    $body = json_decode($json, true);

    echo $json;
?>