<?php
require_once '../connection.php';
require_once '../class/person.php';
require_once '../factory/personfactory.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = [1 => "Send a POST request to this url!"];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $authorID = $_POST['personID'];
    //create author object
    $authorfactory = new AuthorFactory;
    $author = $authorfactory->getNewUser($authorID);

    $title = $_POST['title'];
    $topicOption = $_POST['topic'];
    $documentToUpload = $_FILES['document'];
    $authorRemarks = $_POST['authorRemarks'];

    $document = [
        "title" => $title,
        "topic" => $topicOption,
        "documentToUpload" => $documentToUpload,
        "authorRemarks" => $authorRemarks
    ];

    $documentMetaData = $author->uploadNewDocument($doc);
    $arr = ["message" => "upload successful"];
}

$myfile = fopen("signUp.php", "r") or die("unable to open file");

$doc_arr = [
    "title" => "test289732",
    "topic" => "Science",
    "documentToUpload" => $myfile,
    "authorRemarks" => "test remarks"
];
$authorfactory = new AuthorFactory;
$author = $authorfactory->getNewUser("A1");

$author->uploadNewDocument($doc_arr);
$arr = ["message" => "upload successful"];

echo json_encode($arr, JSON_PRETTY_PRINT);
?>