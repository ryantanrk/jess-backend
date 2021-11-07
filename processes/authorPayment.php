<?php
require_once '../connection.php';
require_once '../class/person.php';
require_once '../factory/personfactory.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = ["1" => "Send a POST request to this url!"];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $arr = "";
    //get author
    $authorID = $_POST['authorID'];
    $documentIDs = $_POST['documentIDs']; //array
    $choice = $_POST['choice']; //choice: pay/cancel

    var_dump($documentIDs);
    $factoryobj = new AuthorFactory;
    $author = $factoryobj->getNewUser($authorID);

    //pay for each doc
    if ($choice === "pay") {
        foreach ($documentIDs as $docID) {
            $author->payDocument($docID);
        }
    }
    else if ($choice === "cancel") {
        foreach ($documentIDs as $docID) {
            $author->cancelPaymentDocument($docID);
        }
    }
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>