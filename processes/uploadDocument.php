<?php
require_once '../connection.php';
require_once '../class/person.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = [1 => "Send a POST request to this url!"];

//not yet tested
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $authorID = $_POST['personID'];
    //create author object
    $author = getPersonFromID($authorID);

    $title = $_POST['title'];
    $topicOption = $_POST['topic'];
    $documentToUpload = $_FILES['document']['tmp_name']; //file
    $authorRemarks = $_POST['authorRemarks'];

    $doc = [
        "authorID" => $authorID,
        "title" => $title,
        "topic" => $topicOption,
        "documentToUpload" => $documentToUpload,
        "authorRemarks" => $authorRemarks
    ];
    $documentMetaData = $author->uploadNewDocument($doc);
    $arr = ["message" => "upload"];
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>