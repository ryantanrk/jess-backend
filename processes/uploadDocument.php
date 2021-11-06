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
    $documentToUpload = $_POST['document']; //file
    $authorRemarks = $_POST['authorRemarks'];

    $doc = [
        "title" => $title,
        "topic" => $topicOption,
        "documentToUpload" => $documentToUpload,
        "authorRemarks" => $authorRemarks
    ];

    $documentMetaData = $author->uploadNewDocument($doc);
    $arr = ["message" => "upload"];
}
//testing code
// $myfile = fopen("testdoc2.pdf", "r") or die("unable to open file");
// $fcontent = fread($myfile, filesize("testdoc2.pdf"));
// fclose($myfile);

// $doc_arr = [
//     "title" => "test289732",
//     "topic" => "Science",
//     "documentToUpload" => $fcontent,
//     "authorRemarks" => "test remarks"
// ];
// $authorfactory = new AuthorFactory;
// $author = $authorfactory->getNewUser("A1");

// $author->uploadNewDocument($doc_arr);
// $arr = ["message" => "upload successful"];

echo json_encode($arr, JSON_PRETTY_PRINT);
?>