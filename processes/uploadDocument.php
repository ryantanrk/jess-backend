<?php
require_once '../connection.php';
require_once '../class/person.php';
session_start();

$userArray = $_SESSION["user"]->getPersonData();
print_r($userArray);

$arr = [1 => "Send a POST request to this url!"];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $authorID = $_POST['personID'];
    $title = $_POST['title'];
    $topicOption = $_POST['topic'];
    $documentToUpload = $_FILES['document'];
    $authorRemarks = $_POST['authorRemarks'];

    $document = [
        "personID" => $authorID,
        "title" => $title,
        "topicOption" => $topicOption,
        "documentToUpload" => $documentToUpload,
        "authorRemarks" => $authorRemarks
    ];

    $documentMetaData = new DocumentMetaData([]);
    $documentMetaData = $_SESSION["user"]->uploadNewDocument($doc);
    $arr = ["message" => "upload successful"];
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>