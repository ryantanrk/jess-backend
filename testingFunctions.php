<?php
	require_once 'class/person.php';
	require_once 'class/document.php';
	require_once 'connection.php';
	require_once 'class/factory.php';

	//editor object
	//$factoryobj = new EditorFactory;
	//author object
	//$factoryobj = new EditorFactory;
	//reviewer object
	//$factoryobj = new EditorFactory;
/*
	//Strictly manuscript section
	$manuscriptDocument = new Document(new ManuscriptState, "D1");

	print_r($manuscriptDocument->getDocumentMetaData());
	writeLine("");
	writeLine("");

	print_r($manuscriptDocument->getDocumentReviews());
	writeLine("");
	writeLine("");

	$manuscriptDocument->setDocumentMetaData("documentStatus", "united");
	$manuscriptDocument->setDocumentReview("R1", "reviewStatus", "also united");
	//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
*/
/*
	//Strictly Journal section
	writeLine("oi");
	$journalDocument = new Document(new JournalState, "D1");
	print_r($journalDocument->getDocumentMetaData());
	writeLine("");
	writeLine("");

	$journalDocument->setDocumentMetaData("journalIssue", "no issue I get it");
	$journalDocument->setDocumentMetaData("printDate", "2020-10-10");

	print_r($journalDocument->getDocumentReviews());
	writeLine("");
	writeLine("");
	//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
*/


	//Author + manuscript specific 
	// $_SESSION["user"] = clientCode(new AuthorFactory(), "A1", "a", "a", "a", "a");
	// $userGet = $_SESSION["user"]->getAuthorizedDocumentAttribute("D1");

	// $_SESSION["user"]->setAuthorizedDocumentAttribute("D1", "documentStatus", "paid");


	//-----------------------------------------------------------------------------------------------------------------------------
	//Reviewer + manuscript specific stuff
	// $_SESSION["user"] = clientCode(new ReviewerFactory(), "R2", "r", "r", "r", "r");

	// $reviewObject = $_SESSION["user"]->getAuthorizedDocumentAttribute("D1");
	// print_r($reviewObject);
	// $_SESSION["user"]->setAuthorizedDocumentAttribute("D1", "rating", "10");
	// $_SESSION["user"]->setAuthorizedDocumentAttribute("D1", "comment", "nice");
	// $_SESSION["user"]->setAuthorizedDocumentAttribute("D1", "reviewStatus", "complete");
	//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

	//Editor + manuscript specific stuff
	//$editorobj = clientCode(new EditorFactory(), "E1", "editor1", md5("password"), "editor1@x.com", "2020-12-02");

	// $userGet = $_SESSION["user"]->getAuthorizedDocumentAttribute("D1");
	// print_r($userGet[0]);
	// echo "<br><br>";
	// print_r($userGet[1]);
	// echo "<br><br>";

	// $editorobj->setAuthorizedDocumentAttribute("D1", "documentStatus", "published");
	// $editorobj->setAuthorizedDocumentAttribute("D1", "printDate", "09-11-2021");
	//$editorobj->setAuthorizedDocumentAttribute("D1", "journalIssue", "xyzy");

	// $_SESSION["user"]->setAuthorizedDocumentAttribute("D1", "price", "100");
	// $_SESSION["user"]->setAuthorizedDocumentAttribute("D1", "documentStatus", "pending payment");
	// $_SESSION["user"]->setAuthorizedReviewAttribute("", "createNewReviewRequest", "D1-R1");
	// $_SESSION["user"]->setAuthorizedReviewAttribute("", "createNewReviewRequest", "D1-R2");
	// $_SESSION["user"]->setAuthorizedReviewAttribute("D1-R1", "reviewStatus", "Accepted");

	//mail test
    // mail("j18026290@student.newinti.edu.my", "test", "ass", "From: survxyzburner@gmail.com");

	//getPersonFromID
	$reviewerobj = getPersonFromID("A1");
	echo json_encode($reviewerobj, JSON_PRETTY_PRINT);
?>