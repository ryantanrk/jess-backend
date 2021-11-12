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
    $editor->approveRejectReviewer($reviewerID, "approved");
}

function rejectReviewer($editorID, $reviewerID) {
    $editor = getPersonFromID($editorID);
    $editor->approveRejectReviewer($reviewerID, "rejected");
}

function approveDocument($editorID, $documentID, $editorRemarks) {
    global $arr;
    $editor = getPersonFromID($editorID);
    
    //get document as well
    $document = retrieveDocumentFromDatabaseInCorrectState($documentID);
    //metadata object
    $metadata = $document->getDocumentMetaData();
    
    //check status
    if ($metadata->documentStatus === "new") {
        //edit attribute array
        $attribute_arr = [
            "editorID" => $editorID,
            "editorRemarks" => $editorRemarks,
            "documentStatus" => "pending review"
        ];

        //add editor remarks, set editor ID, change status to pending review
        foreach ($attribute_arr as $key => $value) {
            $editor->setAuthorizedDocumentAttribute($documentID, $key, $value);
        }
        
        //notify author
        $author = getPersonFromID($metadata->authorID);
        $authordata = $author->getPersonData();
        $message = "Hello " . $authordata['username'] . ",<br/><br/>";
        $message .= "Your uploaded document: " . $metadata->title . ", has been determined to be within scope.<br/>";
        $message .= "Please wait for reviewers to review the document.<br/><br/>";
        $message .= "<b>JESS</b><br/>";
        $message .= "<i>This is an automatically generated email.</i>";
        $arr = ["message" => "document approved: " . $metadata->documentID];

        $editor->notify($authordata['personID'], "Approval of Document: " . $metadata->title, $message);
        
    }
    else {
        $arr = ["error" => "document is not in a state to be approved"];
    }
}

function rejectDocument($editorID, $documentID) {
    global $arr;
    $editor = getPersonFromID($editorID);
    
    //get document as well
    $document = retrieveDocumentFromDatabaseInCorrectState($documentID);
    //metadata object
    $metadata = $document->getDocumentMetaData();
    
    //check status
    if ($metadata->documentStatus === "new") {
        //change status to pending review
        $editor->setAuthorizedDocumentAttribute($documentID, "documentStatus", "rejected");
        //add editorID
        $editor->setAuthorizedDocumentAttribute($documentID, "editorID", $editorID);

        //notify author
        $author = getPersonFromID($metadata->authorID);
        $authordata = $author->getPersonData();
        $message = "Hello " . $authordata['username'] . ",<br/><br/>";
        $message .= "Your uploaded document: " . $metadata->title . ", has been determined to NOT be within scope and has been rejected.<br/>";
        $message .= "This document will no longer be considered for publication. Thank you.<br/><br/>";
        $message .= "<b>JESS</b><br/>";
        $message .= "<i>This is an automatically generated email.</i>";

        $editor->notify($authordata['personID'], "Rejection of Document: " . $metadata->title, $message);
        $arr = ["message" => "document rejected: " . $metadata->documentID];
    }
    else {
        $arr = ["error" => "document is not in a state to be rejected."];
    }
}

//assign single reviewer
function assignReviewer($editorID, $documentID, $reviewerID) {
    global $arr;
    //editor object
    $editor = getPersonFromID($editorID);
    $documentIDreviewerID = $documentID . "-" . $reviewerID;

    //check reviewer status
    $reviewer = getPersonFromID($reviewerID);
    $reviewerdata = $reviewer->getPersonData();

    if ($reviewerdata['status'] === "available") {
        //add to review table
        $editor->setAuthorizedReviewAttribute("", "createNewReviewRequest", $documentIDreviewerID);
        $arr = ["message" => "reviewer " . $reviewerID . " is assigned to review document " . $documentID];

        //set document status
        $editor->setAuthorizedDocumentAttribute($documentID, "documentStatus", "under review");
    }
    else {
        $arr = ["error" => "reviewer " . $reviewerID . " is not available"];
    }
}

function compile($editorID, $documentID, $editorRemarks) {
    global $arr;
    //change editor remarks and document status
    //get editor
    $editor = getPersonFromID($editorID);
    //get document metadata
    $document = retrieveDocumentFromDatabaseInCorrectState($documentID);
    $metadata = $document->getDocumentMetaData();

    if ($metadata->documentStatus === "pending compile") {  
        $document_arr = [
            "editorRemarks" => $editorRemarks,
            "documentStatus" => "pending modify",
            "editDueDate" => date("Y-m-d") + 30
        ];
    
        foreach ($document_arr as $key => $value) {
            $editor->setAuthorizedDocumentAttribute($documentID, $key, $value);
        }
    
        //notify author
        //author object
        $author = getPersonFromID($metadata->authorID);
        $authordata = $author->getPersonData();
    
        $message = "Hello " . $authordata['username'] . ", <br/><br/>";
        $message .= "This email was sent to notify you that your document \"" . $metadata->title . "\" has been approved by reviewers.<br/>";
        $message .= "Please make any necessary modifications to your document and submit within 30 days. Thank you.<br/><br/>";
        $message .= "<b>JESS</b><br/>";
        $message .= "<i>This is an automatically generated email.</i>";
    
        $editor->notify($authordata['personID'], "Pending modify: " . $metadata->title, $message);
        $arr = ["message" => "document " . $documentID . " compiled"];
    }
    else {
        $arr = ["error" => "document is not in a state to be compiled"];
    }
}

function finalCheck($editorID, $documentID, $satisfied) {
    global $arr;
    //set status
    //get editor
    $editor = getPersonFromID($editorID);
    //document data
    $document = retrieveDocumentFromDatabaseInCorrectState($documentID);
    $metadata = $document->getDocumentMetaData();

    $arr = ["error" => "invalid choice"];
    if ($metadata->documentStatus === "pending final check") {
        if ($satisfied === "satisfied") {
            //put on hold
            $editor->setAuthorizedDocumentAttribute($documentID, "documentStatus", "on hold");
            $arr = ["message" => "approved document " . $documentID];
        }
        else if ($satisfied === "unsatisfied") {
            //reject and notify user
            //document data
            $document = retrieveDocumentFromDatabaseInCorrectState($documentID);
            $metadata = $document->getDocumentMetaData();
            
            //user data
            $author = getPersonFromID($metadata->authorID);
            $authordata = $author->getPersonData();
    
            $editor->setAuthorizedDocumentAttribute($documentID, "documentStatus", "rejected");
    
            $message = "Hello " . $authordata['username'] . ", <br/><br/>";
            $message .= "This email was sent to notify you that your final document \"" . $metadata->title . "\" has been rejected.<br/>";
            $message .= "This document will no longer be considered for publication. Thank you.<br/><br/>";
            $message .= "<b>JESS</b><br/>";
            $message .= "<i>This is an automatically generated email.</i>";
    
            $editor->notify($authordata['personID'], "Document Rejected: " . $metadata->title, $message);
    
            $arr = ["message" => "rejected document " . $documentID];
        }
    }
    else {
        $arr = ["error" => "document is not in a state to be confirmed (final check)"];
    }
}

function setPrice($editorID, $documentID, $price) {
    global $arr;
    //set price and set status to pending payment
    $editor = getPersonFromID($editorID);
    //document data
    $document = retrieveDocumentFromDatabaseInCorrectState($documentID);
    $metadata = $document->getDocumentMetaData();

    if ($metadata->documentStatus === "on hold") {
        $editor->setAuthorizedDocumentAttribute($documentID, "price", $price);
        $editor->setAuthorizedDocumentAttribute($documentID, "documentStatus", "pending payment");

        //notify user
        //get document data
        $document = retrieveDocumentFromDatabaseInCorrectState($documentID);
        $metadata = $document->getDocumentMetaData();

        //get user data
        $author = getPersonFromID($metadata->authorID);
        $authordata = $author->getPersonData();
        $message = "Hello " . $authordata['username'] . ", <br/><br/>";
        $message .= "This email was sent to notify you that your final document \"" . $metadata->title . "\" has been approved.<br/>";
        $message .= "A price of RM" . $price . " has been set for your document. Please make your payment for publication.<br/>";
        $message .= "Thank you.<br/><br/>";
        $message .= "<b>JESS</b><br/>";
        $message .= "<i>This is an automatically generated email.</i>";

        $editor->notify($authordata['personID'], "Document Approved: " . $metadata->title, $message);

        $arr = ["message" => "price of " . $price . " has been set for document " . $documentID];
    }
    else {
        $arr = ["error" => "document is not in a state to set price"];
    }
}

function publishDocument($editorID, $documentID, $journalIssue) {
	global $arr;
	$editor = getPersonFromID($editorID);
    //document data
    $document = retrieveDocumentFromDatabaseInCorrectState($documentID);
    $metadata = $document->getDocumentMetaData();

    if ($metadata->documentStatus === "paid") {
        $docAttributes = [
            "documentStatus" => "published",
            "printDate" => date("Y-m-d"),
            "journalIssue" => $journalIssue
        ];

        foreach ($docAttributes as $key => $value) {
            $editor->setAuthorizedDocumentAttribute($documentID, $key, $value);
        }
        $arr = ["message" => "published document: " . $documentID];
    }
    else if ($metadata->documentStatus === "published") {
        $arr = ["error" => "document is already published"];
    }
    else {
        $arr = ["error" => "document is not in a state to be published"];
    }
}

//all editor functions
//function, editorID attribute is MANDATORY
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch ($_POST['function']) {
        case "approvereviewer":
            approveReviewer($_POST['editorID'], $_POST['reviewerID']);
            break;
        case "rejectreviewer":
            rejectReviewer($_POST['editorID'], $_POST['reviewerID']);
            break;
        case "determinescope":
            if ($_POST['scope'] === "Within Scope") {
                approveDocument($_POST['editorID'], $_POST['documentID'], $_POST['editorRemarks']);
            }
            else if ($_POST['scope'] === "Out of scope") {
                rejectDocument($_POST['editorID'], $_POST['documentID']);
            }
            break;
        case "assignreviewers":
            assignReviewer($_POST['editorID'], $_POST['documentID'], $_POST['reviewerID']);
            break;
        case "compile":
            compile($_POST['editorID'], $_POST['documentID'], $_POST['editorRemarks']);
            break;
        case "finalcheck":
            finalCheck($_POST['editorID'], $_POST['documentID'], $_POST['satisfied']);
            break;
        case "setprice":
            setPrice($_POST['editorID'], $_POST['documentID'], $_POST['price']);
            break;
        case "publish":
            publishDocument($_POST['editorID'], $_POST['documentID'], $_POST['journalIssue']);
            break;
    }
}

echo json_encode($arr, JSON_PRETTY_PRINT);
?>