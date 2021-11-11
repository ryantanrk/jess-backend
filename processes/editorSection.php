<?php
//require_once 'the session key';
require_once '../connection.php';
require_once '../class/person.php';
require_once '../class/factory.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$arr = ["1" => "Send a POST request to this url!"];

function approveReviewer($editorID, $reviewerID) {
    $editor = getPersonFromID($editorID);
    $editor->approveReviewer($reviewerID);
}

function approveDocument($editorID, $documentID) {
    global $arr;
    $editor = getPersonFromID($editorID);
    
    //get document as well
    $document = new Document(new ManuscriptState, $documentID);
    //metadata object
    $metadata = $document->documentMetaDataObject;
    
    //check status
    if ($metadata->documentStatus === "new") {
        //change status to pending review
        $editor->setAuthorizedDocumentAttribute($documentID, "documentStatus", "pending review");
        //notify author
        $author = getPersonFromID($metadata->authorID);
        $message = "Hello " . $author->username . ",<br/>";
        $message .= "Your uploaded document: " . $metadata->title . ", has been determined to be within scope.<br/><br/>";
        $message .= "JESS<br/>";
        $message .= "<i>This is an automatically generated email.</i>";

        $editor->notify($author->email, "Approval of Document: " . $metadata->title, $message);
        $arr = ["message" => "document approved: " . $metadata->documentID];
    }
}

function rejectDocument($editorID, $documentID) {
    global $arr;
    $editor = getPersonFromID($editorID);
    
    //get document as well
    $document = new Document(new ManuscriptState, $documentID);
    //metadata object
    $metadata = $document->documentMetaDataObject;
    
    //check status
    if ($metadata->documentStatus === "new") {
        //change status to pending review
        $editor->setAuthorizedDocumentAttribute($documentID, "documentStatus", "rejected");
        //notify author
        $author = getPersonFromID($metadata->authorID);
        $message = "Hello " . $author->username . ",<br/>";
        $message .= "Your uploaded document: " . $metadata->title . ", has been determined to NOT be within scope and is rejected.<br/><br/>";
        $message .= "JESS<br/>";
        $message .= "<i>This is an automatically generated email.</i>";

        $editor->notify($author->email, "Rejection of Document: " . $metadata->title, $message);
        $arr = ["message" => "document rejected: " . $metadata->documentID];
    }
}

//all editor functions
//function is MANDATORY
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch ($_POST['function']) {
        case "approvereviewer":
            approveReviewer($_POST['editorID'], $_POST['reviewerID']);
            break;
        case "determinescope":
            if ($_POST['scope'] === "Within Scope") {
                approveDocument($_POST['editorID'], $_POST['documentID']);
            }
            else if ($_POST['scope'] === "Out of scope") {
                rejectDocument($_POST['editorID'], $_POST['documentID']);
            }
            break;
        case "assignreviewers":
            break;
    }
}

if($request[0] == "mainPage")
{

}

if($request[0] == "assignReviewers")
{

}

if($request[0] == "finalCheck")
{

}

if($request[0] == "publish")
{

}

if($request[0] == "updateManuscriptInfo")
{

}

if($request[0] == "manageLatePeople")
{

}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>