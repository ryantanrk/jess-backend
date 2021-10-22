<?php
	require_once 'document.php';

	// ------------------------------------------------------------------------------------------------ The client code.

	$DocumentObject = new Document(new ManuscriptState());
	echo "Document object created <br><br>";

	//---------------------------------------------------------------------------------------
	$dmd = array(
		"documentID" => "doc1", 
		"title" => "docTitle1", 
		"topic" => "docTopic1", 
		"pages" => "docPages1", 
		"dateOfSubmission" => "docDateOfSubmission1", 
		"status" => "docStatus1", 
		"mainAuthor" => "docMainAuthor1", 
		"authorRemarks" => "docAuthorRemarks1", 
		"editorRemarks" => "docEditorRemarks1");

	$DocumentObject->setDocumentMetaData($dmd);
	echo "Document object meta data set <br><br>";

	$DocumentObject->getDocumentMetaData();
	echo "Retrieved Document object meta data<br><br>";

	echo "<br><br>". "----------------------------------------------------------------------------------------" ."<br><br>";
	//---------------------------------------------------------------------------------------
	$d1r1 = array("reviewer" => "reviewer1", "rating" => "doc1Reviewer1's rating", "comments" => "doc1Reviewer1's Comments");
	$DocumentObject->setDocumentReviews($d1r1);
	echo "Document object reviewBlock1 sent <br><br>";

	$d1r2 = array("reviewer" => "reviewer2", "rating" => "doc1Reviewer2's rating", "comments" => "doc1Reviewer2's Comments");
	$DocumentObject->setDocumentReviews($d1r2);
	echo "Document object reviewBlock2 sent <br><br>";

	$DocumentObject->getDocumentReviews();

	echo "Dah <br><br>";
?>