<?php
//require_once 'the session key';
require_once '../connection.php';
require_once '../class/document.php';
require_once '../class/person.php';

//-----------------------------------------------------------------------------------------------
//Abstract retrieve function initiated. Array of commands unpackaged from JSON object in to $request
//Examples of $request
//mainPage
// $request = ["request" => "mainPage"]; 

//updateReviewerData
// $request = ["request" => "updateReviewerData", 
// 			"reviewerID" => "R1", 
// 			"newUserName" => "REVIEWER1", 
// 			"newPassword" => "PASSWORD", 
// 			"newEmail" => "REVIEWER1@X.COM", 
// 			"newDob" => "2000-2-2"]; 

//signOut

//getContent
// $request = ["request" => "getContent", "reviewerID" => "R1", "documentID" => D1"];	

//rate
$request = ["request" => "rate", "reviewerID" => "R1", "documentID" => "D1", "rating" => "9", "comment" => "NEIN NEIN NEIN"];
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

//Reviewer object created
$result = sqlProcesses("SELECT * FROM `person` WHERE `personID` = ?", "s", array($request["reviewerID"]));
$reviewerInfo = mysqli_fetch_assoc($result);
$reviewerObject = new Reviewer($request["reviewerID"], $reviewerInfo["username"], $reviewerInfo["password"], $reviewerInfo["email"], $reviewerInfo["dob"]);

//Documents that a particular reviewer is allowed to interact with
$reviewerInvolvedDocumentsWithoutContent = [];

$paramVariablesArray = [$request["reviewerID"]];

$sqlQuery = "SELECT `reviewerID`, review.documentID as `documentID`, `topicID`, `authorID`, `title`, `dateOfSubmission`, `pages`, `topic`, `authorRemarks`, `editorRemarks`, `status`, `rating`, `comment` 
			 FROM `review` 
			 INNER JOIN `document` ON review.documentID = document.documentID
			 WHERE `reviewerID` = ?";

$result = sqlProcesses($sqlQuery, "s", $paramVariablesArray);

if(mysqli_num_rows($result) > 0)
{
	while($reviewerInvolvements = mysqli_fetch_assoc($result))
	{
		//documentMetaData prepared
		$dmdArray = array(
		  "documentID" => $reviewerInvolvements["documentID"], 
		  "topicID" => $reviewerInvolvements["topicID"], 
		  "authorID" => $reviewerInvolvements["authorID"], 
		  "title" => $reviewerInvolvements["title"], 
		  "dateOfSubmission" => $reviewerInvolvements["dateOfSubmission"], 
		  "pages" => $reviewerInvolvements["pages"], 
		  "topic" => $reviewerInvolvements["topic"], 
		  "authorRemarks" => $reviewerInvolvements["authorRemarks"], 
		  "editorRemarks" => $reviewerInvolvements["editorRemarks"],
		  "status" => $reviewerInvolvements["status"]
		);

		//documentReviews prepared
		$drArray = array("reviewerID" => $reviewerInvolvements["reviewerID"], 
						 "rating" => $reviewerInvolvements["rating"], 
						 "comment" => $reviewerInvolvements["comment"]);		

		$documentAndPersonalReviewObject = new Document(new ManuscriptState());
		
		$documentAndPersonalReviewObject->setDocumentMetaData($dmdArray);
		$documentAndPersonalReviewObject->setDocumentReviews($drArray);

		array_push($reviewerInvolvedDocumentsWithoutContent, $documentAndPersonalReviewObject);
	}
}
else
	echo "Reviewer has not been involved in any reviewing yet.<br>";	

// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Metadata & personal reviews of authorized documents retrieved

if($request["request"] == "mainPage")
{
	//echo back the json object of $reviewerInvolvedDocumentsWithoutContent
}
// else if($request["request"] == "updateReviewerData")
// {
// 	$reviewerObject->updatePersonData($request["reviewerID"], $request["newUserName"], $request["newPassword"], $request["newEmail"], $request["newDob"]);
// 	print_r($reviewerObject->getPersonData());

// 	$paramVariablesArray = [$request["newUserName"], $request["newPassword"], $request["newEmail"], $request["newDob"], $request["reviewerID"]];
// 	$sqlQuery = "UPDATE `person` SET `username`=?,`password`=?,`email`=?,`dob`=? WHERE `personID`=?";
// 	sqlProcesses($sqlQuery, "sssss", $paramVariablesArray);
// }
// else if($request["request"] == "signOut")
// {
// 	//terminate session
// }
// else if($request["request"] == "getContent")
// {
// 	writeLine($request["documentID"]);
// 	writeLine("");
// 	writeLine("");

// 	$result = sqlProcesses("SELECT `file` FROM `document` WHERE `documentID` = ?", "s", array($request["documentID"]));
// 	$value = mysqli_fetch_assoc($result);
// 	$fileContent = $value['file'];	

// 	$documentAndPersonalReviewObject->setDocumentContent(array("fileContent"=>$fileContent));
// 	//echo back the json object of $reviewerInvolvedDocumentsWithoutContent
// }
else if($request["request"] == "rate")
{
	writeLine("oi");
	$reviewerInvolvedDocumentsWithoutContent[0]->setDocumentReviews(["reviewerID" => "R1", "rating" => "9", "comment" => "NEIN NEIN NEIN"]);
	$reviewerInvolvedDocumentsWithoutContent[0]->setDocumentReviews(["reviewerID" => "R2", "rating" => "8", "comment" => "EIGHT EIGHT EIGHT"]);
	$reviewerInvolvedDocumentsWithoutContent[0]->setDocumentReviews(["reviewerID" => "R3", "rating" => "7", "comment" => "CHAT CHAT CHAT"]);
	writeLine("");
	$reviewerInvolvedDocumentsWithoutContent[0]->getDocumentReviews([$request["reviewerID"]]);
	$reviewerInvolvedDocumentsWithoutContent[0]->getDocumentReviews([$request["reviewerID"], "R2", "R3"]);
}
?>