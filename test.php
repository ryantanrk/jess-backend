<?php
    require_once 'connection.php';
    require_once 'class/document.php';
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

    $document = new Document("D2");

    echo json_encode($document, JSON_PRETTY_PRINT);
?>