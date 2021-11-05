<?php
class DocumentMetadata
{
	public $documentID;
	public $authorID;
	public $authorUsername;
	public $editorID;
	public $title;
	public $topic;
	public $dateOfSubmission;
	public $printDate;
	public $authorRemarks;
	public $editorRemarks;
	public $reviewDueDate;
	public $editDueDate;
	public $price;
	public $journalIssue;
	public $documentStatus;

	//Initialize the attributes on creation. Can initialize everything, somethings or nothing
	function __construct($metaDataArray)
	{
		$this->documentID = $metaDataArray["documentID"];
		$this->authorID = $metaDataArray["authorID"];
		$this->authorUsername = $metaDataArray["authorUsername"];
		$this->editorID = $metaDataArray["editorID"];
		$this->title = $metaDataArray["title"];
		$this->topic = $metaDataArray["topic"];
		$this->dateOfSubmission = $metaDataArray["dateOfSubmission"];
		$this->authorRemarks = $metaDataArray["authorRemarks"];
		$this->editorRemarks = $metaDataArray["editorRemarks"];
		$this->reviewDueDate = $metaDataArray["reviewDueDate"];
		$this->editDueDate = $metaDataArray["editDueDate"];
		$this->price = $metaDataArray["price"];
		$this->documentStatus = $metaDataArray["documentStatus"];

		if ($this->documentStatus == "Published") {
			$this->printDate = $metaDataArray["printDate"];
			$this->journalIssue = $metaDataArray["journalIssue"];
		}
	}

	//Update metadata 1 attribute at a time
	public function setMetaData($attribute, $value)
	{
		if($attribute == "documentID")
			$this->documentID = $value;
		else if($attribute == "authorID")
			$this->authorID = $value;	
		else if($attribute == "editorID")
			$this->editorID = $value;				
		else if($attribute == "title")
			$this->title = $value;
		else if($attribute == "topic")
			$this->topic = $value;	
		else if($attribute == "dateOfSubmission")
			$this->dateOfSubmission = $value;	
		else if($attribute == "printDate")
			$this->printDate = $value;
		else if($attribute == "authorRemarks")
			$this->authorRemarks = $value;
		else if($attribute == "editorRemarks")
			$this->editorRemarks = $value;	
		else if($attribute == "reviewDueDate")
			$this->reviewDueDate = $value;
		else if($attribute == "editDueDate")
			$this->editDueDate = $value;	
		else if($attribute == "price")
			$this->price = $value;
		else if($attribute == "journalIssue")
			$this->journalIssue = $value;		
		else if($attribute == "documentStatus")
			$this->documentStatus = $value;	

		sqlProcesses("UPDATE `document` SET `{$attribute}` = ? WHERE `documentID`= ?", "ss", [$value, $this->documentID]);
	}

	public function getMetaData()
	{
		$metaDataArray = array(
			"documentID" => $this->documentID, 
			"authorID" => $this->authorID,
			"authorUsername" => $this->authorUsername,
			"editorID" => $this->editorID, 
			"title" => $this->title, 
			"topic" => $this->topic,
			"dateOfSubmission" => $this->dateOfSubmission, 
			"printDate" => $this->printDate, 
			"authorRemarks" => $this->authorRemarks, 
			"editorRemarks" => $this->editorRemarks, 
			"reviewDueDate" => $this->reviewDueDate, 
			"editDueDate" => $this->editDueDate,
			"price" => $this->price, 
			"journalIssue" => $this->journalIssue, 
			"documentStatus" => $this->documentStatus
		);
		// print_r($metaDataArray);
		
		return $this;
	}
} 
?>
