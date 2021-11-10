<?php
require_once '../connection.php';
require_once '../class/person.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = [1 => "Send a POST request to this url!"];

//not tested, prob need fixing
//EDITOR DOCUMENT MODIFICATION
//allows to edit documentID, and editor remarks
function modifyDocument($editorID, $documentID, $newDocumentID, $editorRemarks) {
    global $arr;
    //create author object
    $editor = getPersonFromID($editorID);

    //create document object
    //$documentobj = new Document(new ManuscriptState, $documentID);

    // $authorRemarks = $_POST['authorRemarks'];
    // $documentToUpload = $_FILES['document']['tmp_name']; //file

    $doc = [
        "documentID" => $newDocumentID,
        "editorRemarks" => $editorRemarks,
        "documentStatus" => "pending review"
    ];

    foreach ($doc as $key => $value) {
        $editor->setAuthorizedDocumentAttribute($documentID, $key, $value);
    }

    $arr = ["message" => "edit"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $authorID = $_POST['personID'];
    $documentID = $_POST['documentID'];
    $newDocumentID = $_POST['newDocumentID'];
    $editorRemarks = $_POST['editorRemarks'];
    
    modifyDocument($authorID, $documentID, $newDocumentID, $editorRemarks);
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