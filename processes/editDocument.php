<?php
require_once '../connection.php';
require_once '../class/person.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = [1 => "Send a POST request to this url!"];

//not tested, prob need fixing
//AUTHOR DOCUMENT MODIFICATION
function editDocument($authorID, $documentID, $authorRemarks, $document) {
    global $arr;
    //create author object
    $author = getPersonFromID($authorID);

    $doc = [
        "authorRemarks" => $authorRemarks,
        "file" => $document,
        "documentStatus" => "pending final check"
    ];

    foreach ($doc as $key => $value) {
        $author->setAuthorizedDocumentAttribute($documentID, $key, $value);
    }

    $arr = ["message" => "edit"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $authorID = $_POST['personID'];
    $documentID = $_POST['documentID'];
    $authorRemarks = $_POST['authorRemarks'];
    $documentToUpload = $_FILES['document']['tmp_name']; //file
    
    editDocument($authorID, $documentID, $authorRemarks, $documentToUpload);
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>