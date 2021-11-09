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

    //create document object
    $documentobj = new Document(new ManuscriptState);
    $documentobj->documentStateObject->getDocumentById($documentID);

    $authorRemarks = $_POST['authorRemarks'];
    $documentToUpload = $_FILES['document']['tmp_name']; //file

    $doc = [
        "authorRemarks" => $authorRemarks,
        "file" => $documentToUpload,
        "documentStatus" => "pending final check"
    ];

    foreach ($doc as $key => $value) {
        $author->setDocument($documentobj->documentMetaDataObject, $key, $value);
    }

    // $documentMetaData = $author->editDocument($doc);
    $arr = ["message" => "edit"];
}

// public function editDocument($doc)
// {
//     global $documentTable;
//     $authorID = $this->personID;
//     $documentID = $doc['documentID'];
//     $authorRemarks = $doc['authorRemarks'];
//     $documentStatus = 'pending final check';
    
//     $fileToUpload = $doc["documentToUpload"];

//     $sql = "UPDATE `$documentTable` SET
//             `authorRemarks` = ?, `documentStatus` = ?, `file` = ? 
//             WHERE `documentID` = ? AND `authorID` = ?";

//     $paramVariablesArray = array(
//         $authorRemarks, $documentStatus, $fileToUpload,
//         $documentID, $authorID
//     );

//     sqlProcesses($sql, "sssss", $paramVariablesArray); 
// }

echo json_encode($arr, JSON_PRETTY_PRINT);
?>