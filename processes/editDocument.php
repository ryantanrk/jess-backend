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
    $documentID = $_POST['documentID'];
    //create author object
    $authorfactory = new AuthorFactory;
    $author = $authorfactory->getNewUser($authorID);

    $authorRemarks = $_POST['authorRemarks'];

    $doc = [
        "documentID" => $documentID,
        "authorRemarks" => $authorRemarks
    ];
    //file is optional
    if (is_uploaded_file($_FILES['document']['tmp_name'])) {
        $documentToUpload = file_get_contents($_FILES['document']['tmp_name']); //file
        $doc['documentToUpload'] = $documentToUpload;
    }

    $documentMetaData = $author->editDocument($doc);
    $arr = ["message" => "edit"];
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>