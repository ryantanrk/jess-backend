<?php
    //get received input and returns an associative array
    $body = json_decode(file_get_contents('php://input'), true);

    echo json_encode($body, JSON_PRETTY_PRINT);
?>