<?php
require_once '../connection.php';
require_once '../class/person.php';
require_once '../factory/personfactory.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = ["1" => "Send a POST request to this url!"];

function payDocument($authorID, $documentID, $choice) {
    global $documentTable, $arr;
    $factoryobj = new AuthorFactory;
    $author = $factoryobj->getNewUser($authorID);

    $sql = "SELECT `documentStatus` FROM `$documentTable` WHERE `documentID` = ? AND `authorID` = ?";

    $result = sqlProcesses($sql, "ss", [$documentID, $authorID]);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $status = $row['documentStatus'];
        //create document object
        if ($status === "pending payment") {
            $documentobj = new Document(new ManuscriptState);
            $documentobj->documentStateObject->getDocumentById($documentID);
            if ($choice === "pay") {
                $author->setDocument($documentobj->documentMetaDataObject, "documentStatus", "paid");
                $arr = ["message" => "payment success: " . $documentID];
            }
            else if ($choice === "cancel") {
                $author->setDocument($documentobj->documentMetaDataObject, "documentStatus", "cancelled");
                $arr = ["message" => "payment cancelled: " . $documentID];
            }
            else {
                $arr = ["error" => "choice is incorrectly defined"];
            }
        }
        else if ($status === "paid") {
            $arr = ["error" => "document has already been paid for"];
        }
        else if ($status === "cancelled") {
            $arr = ["error" => "document payment has already been cancelled"];
        }
        else if ($status === "published") {
            //error
            $arr = ["error" => "document is published"];
        }
        else {
            $arr = ["error" => "please ensure document status is correct and try again"];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //get author
    $authorID = $_POST['authorID'];
    $documentID = $_POST['documentID'];
    $choice = $_POST['choice']; //choice: pay/cancel

    payDocument($authorID, $documentID, $choice);
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>