<?php
require_once 'person.php';

abstract class DocumentAttributes
{

}

class DocumentMetaData extends DocumentAttributes
{
	//Make it private later. It's an excuse for data hiding
	public $documentID;
	public $authorID;
	public $authorUsername;
	public $editorID;
	public $editorUsername;
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
		if(sizeof($metaDataArray) != 0)
		{
			$this->documentID = $metaDataArray["documentID"];
			$this->authorID = $metaDataArray["authorID"];
			$this->authorUsername = $metaDataArray["authorUsername"];
			$this->editorID = $metaDataArray["editorID"];
			$this->editorUsername = $metaDataArray["editorUsername"];
			$this->title = $metaDataArray["title"];
			$this->topic = $metaDataArray["topic"];
			$this->dateOfSubmission = $metaDataArray["dateOfSubmission"];
			$this->printDate = $metaDataArray["printDate"];
			$this->authorRemarks = $metaDataArray["authorRemarks"];
			$this->editorRemarks = $metaDataArray["editorRemarks"];
			$this->reviewDueDate = $metaDataArray["reviewDueDate"];
			$this->editDueDate = $metaDataArray["editDueDate"];
			$this->price = $metaDataArray["price"];
			$this->journalIssue = $metaDataArray["journalIssue"];
			$this->documentStatus = $metaDataArray["documentStatus"];
		}
	}

	//Update metadata 1 attribute at a time
	public function setMetaData($attribute, $value)
	{
		global $personTable, $documentTable;
		if($attribute === "documentID")
			$this->documentID = $value;
		else if($attribute === "authorID") {
			$this->authorID = $value;

			//get author username as well
			$sql = "SELECT `username` FROM `$personTable` WHERE `personID` = ?";
			$results = sqlProcesses($sql, "s", [$this->authorID]);
			$this->authorUsername = mysqli_fetch_assoc($results)['username'];
		}
		else if($attribute === "editorID") {
			$this->editorID = $value;

			//get editor username as well
			$sql = "SELECT `username` FROM `$personTable` WHERE `personID` = ?";
			$results = sqlProcesses($sql, "s", [$this->editorID]);
			$this->editorUsername = mysqli_fetch_assoc($results)['username'];
		}
		else if($attribute === "title")
			$this->title = $value;
		else if($attribute === "topic")
			$this->topic = $value;	
		else if($attribute === "dateOfSubmission")
			$this->dateOfSubmission = $value;	
		else if($attribute === "printDate")
			$this->printDate = $value;
		else if($attribute === "authorRemarks")
			$this->authorRemarks = $value;
		else if($attribute === "editorRemarks")
			$this->editorRemarks = $value;	
		else if($attribute === "reviewDueDate")
			$this->reviewDueDate = $value;
		else if($attribute === "editDueDate")
			$this->editDueDate = $value;				
		else if($attribute === "price")
			$this->price = $value;
		else if($attribute === "journalIssue")
			$this->journalIssue = $value;		
		else if($attribute === "documentStatus")
			$this->documentStatus = $value;	

		sqlProcesses("UPDATE `$documentTable` SET `{$attribute}` = ? WHERE `documentID`= ?", 
					"ss", [$value, $this->documentID]);
	}

	public function getMetaData()
	{
		$metaDataArray = array(
			"documentID" => $this->documentID, 
			"authorID" => $this->authorID, "authorUsername" => $this->authorUsername,
			"editorID" => $this->editorID, "editorUsername" => $this->editorUsername, 
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
		
		return $metaDataArray;
	}
} 

class DocumentReview extends DocumentAttributes
{
	public $documentID;
	public $reviewerID;
	public $rating;
	public $comment;
	public $reviewStatus;
	public $dateOfReviewCompletion;

	//Initialize the attributes on creation. Can initialize everything, somethings or nothing
	function __construct($reviewDataArray)
	{
		if(sizeof($reviewDataArray) != 0)
		{
			$this->documentID = $reviewDataArray["documentID"];
			$this->reviewerID = $reviewDataArray["reviewerID"];			
			$this->rating = $reviewDataArray["rating"];
			$this->comment = $reviewDataArray["comment"];
			$this->reviewStatus = $reviewDataArray["reviewStatus"];
			$this->dateOfReviewCompletion = $reviewDataArray["dateOfReviewCompletion"];
		}
	}

	//Update metadata 1 attribute at a time
	public function setReviewData($attribute, $value)
	{
		if($attribute == "rating") 
			$this->rating = $value;
		
		else if($attribute == "comment") 
			$this->comment = $value;	

		else if($attribute == "reviewStatus") 
			$this->reviewStatus = $value;
		
		else if($attribute == "dateOfReviewCompletion") 
			$this->dateOfReviewCompletion = $value;			

		sqlProcesses("UPDATE `review` SET `{$attribute}` = ? WHERE `documentID`= ? AND `reviewerID` = ?", "sss", [$value, $this->documentID, $this->reviewerID]);
	}

	public function getReviewData()
	{
		$reviewDataArray = array(
								"documentID" => $this->documentID,
								"reviewerID" => $this->reviewerID,
								"rating" => $this->rating,
								"comment" => $this->comment, 
								"reviewStatus" => $this->reviewStatus, 
								"dateOfReviewCompletion" => $this->dateOfReviewCompletion, 
							);

		return $reviewDataArray;
	}
}

//Document class has a documentStateObject
class Document
{
	public $documentStateObject;
	public $documentMetaDataObject;
	public $DocumentReviewsArray = [];

	//------------------------------------------------------------------ State functions
	//Look at this, most of the operations within these functions are ran by the document state object
	public function __construct(DocumentState $documentStateObject)
	{
		$this->documentStateObject = $documentStateObject;
		$this->documentStateObject->stateSetDocument($this);
		$this->documentMetaDataObject = new DocumentMetaData([]);
		$this->DocumentReviewsArray = [];
	}

	public function setDocumentMetaData($objectMetaData, $attribute, $value) {
		$this->documentStateObject->setDocumentMetaData($attribute, $value);
	}
	
	public function setDocumentReviews($targetReviewObject, $attribute, $value){
		$this->documentStateObject->setDocumentReviews($targetReviewObject, $attribute, $value);
	}

	public function addDocumentReview(DocumentReview $targetReviewObject) {
		$this->documentStateObject->addDocumentReview($targetReviewObject);
	}

	public function getDocumentMetaData($documentID){return $this->documentStateObject->getDocumentMetaData($documentID);}
	public function getDocumentReviews($reviewerIDArray){return $this->documentStateObject->getDocumentReviews($reviewerIDArray);}

	//State function
	public function getDocumentStateClass() : string {return get_class($this->documentStateObject);}
}

//DocumentState class has a DocumentObject
abstract class DocumentState implements JsonSerializable
{
	protected $documentObject;

	public function jsonSerialize() {
		return get_class($this);
	}

	public function stateSetDocument(Document $documentObject){$this->documentObject = $documentObject;}

	abstract public function getDocumentById($documentID);

	abstract public function setDocumentMetaData($attribute, $value);
	abstract public function setDocumentReviews($targetReviewObject, $attribute, $value);
	abstract public function addDocumentReview(DocumentReview $review);

	abstract public function getDocumentMetaData($documentID);
	abstract public function getDocumentReviews($documentID);	
}

//-------------------------------------------------------------------------------------------------------- "Concrete" documents

//ManuscriptState class
class ManuscriptState extends DocumentState
{
	public function getDocumentById($documentID) {
		global $documentTable, $personTable, $reviewTable;
		$query = "SELECT * FROM `$documentTable` WHERE `documentID` = ?";

		$result = sqlProcesses($query, "s", [$documentID]);

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            //get data
            $documentID = $row['documentID'];

			//document metadata
			$this->getDocumentMetaData($documentID);
            
            //set reviews
			$this->documentObject->DocumentReviewsArray = $this->getDocumentReviews($documentID);
		}
	}

	public function setDocumentMetaData($attribute, $value){
		$this->documentObject->documentMetaDataObject->setMetaData($attribute, $value);
	}

	public function setDocumentReviews($targetReviewObject, $attribute, $value)
	{
		$this->documentObject->DocumentReviewsArray[$targetReviewObject]->setReviewData($attribute, $value);
	}

	public function addDocumentReview(DocumentReview $review) {
		array_push($this->documentObject->DocumentReviewsArray, $review);
	}

	public function getDocumentMetaData($documentID)
	{
		global $documentTable, $personTable;
		$sql = "SELECT `documentID`, `authorID`, `username` AS `authorUsername`, `editorID`, `title`, `topic`, 
				`dateOfSubmission`, `printDate`, `authorRemarks`, `editorRemarks`, `reviewDueDate`, 
				`editDueDate`, `price`, `journalIssue`, `documentStatus` 
				FROM `$documentTable` D JOIN `$personTable` P ON D.authorID = P.personID WHERE `documentID` = ?";

		$results = sqlProcesses($sql, "s", [$documentID]);

		$metaDataArray = [];
		$editorID = "";
		if(mysqli_num_rows($results) > 0) {
			$metaDataArray = mysqli_fetch_assoc($results);
			$metaDataArray['editorUsername'] = null;
			if (isset($metaDataArray['editorID'])) {
				$editorID = $metaDataArray["editorID"];

				//get editor username
				$sqlEditor = "SELECT `username` FROM `$personTable` WHERE `personID` = ?";
				$resultEditor = sqlProcesses($sqlEditor, "s", [$editorID]);

				if (mysqli_num_rows($resultEditor) > 0) {
					//if result
					$editorUsername = mysqli_fetch_assoc($resultEditor)['username'];
					$metaDataArray['editorUsername'] = $editorUsername;
					
				}
			}
		}

		//Document meta data attribute initialized
		$this->documentObject->documentMetaDataObject = new DocumentMetaData($metaDataArray);

		$this->documentObject->documentMetaDataObject->getMetaData();
		//Prepare the meta data information to be manuscript specific
		$this->documentObject->documentMetaDataObject->setMetaData("printDate", "");
		$this->documentObject->documentMetaDataObject->setMetaData("journalIssue", "");

		return $this->documentObject->documentMetaDataObject;
	}

	public function getDocumentReviews($documentID)
	{
		$sql = "SELECT * FROM `review` WHERE `documentID` = ?";

		$results = sqlProcesses($sql, "s", [$documentID]);

		$reviewsObjectArray = [];

		if(mysqli_num_rows($results) > 0)
		{
			while($individualReview = mysqli_fetch_assoc($results))
			{
				$individualReviewObject = new DocumentReview($individualReview);
				array_push($reviewsObjectArray, $individualReviewObject);
			}
		}

		return $reviewsObjectArray;
	}
}

////JournalState class
class JournalState extends DocumentState
{
	public function concreteTransform(): void
	{
		echo "JournalState transform to Manuscript via the state's document object's transition function. <br>";
		$this->documentObject->transitionTo(new ManuscriptState());
	}

	public function getDocumentById($documentID) {
		global $documentTable;
		$query = "SELECT * FROM `$documentTable` WHERE `documentID` = ?";

		$result = sqlProcesses($query, "s", [$documentID]);

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//get data
			$documentID = $row['documentID'];

			//document metadata
			$this->getDocumentMetaData($documentID);
			
			//set reviews
			$this->documentObject->DocumentReviewsArray = $this->getDocumentReviews($documentID);
		}
	}

	public function setDocumentMetaData($attribute, $value){$this->documentObject->documentMetaDataObject->setMetaData($attribute, $value);}

	public function getDocumentMetaData($documentID)
	{
		global $documentTable, $personTable;
		$sql = "SELECT `documentID`, `authorID`, `username` AS `authorUsername`, `editorID`, `title`, `topic`, 
		`dateOfSubmission`, `printDate`, `authorRemarks`, `editorRemarks`, `reviewDueDate`, 
		`editDueDate`, `price`, `journalIssue`, `documentStatus` 
		FROM `$documentTable` D JOIN `$personTable` P ON D.authorID = P.personID WHERE `documentID` = ?";

		$results = sqlProcesses($sql, "s", [$documentID]);

		$metaDataArray = [];
		$editorID = "";
		if(mysqli_num_rows($results) > 0) {
			$metaDataArray = mysqli_fetch_assoc($results);
			$metaDataArray['editorUsername'] = null;
			if (isset($metaDataArray['editorID'])) {
				$editorID = $metaDataArray["editorID"];

				//get editor username
				$sqlEditor = "SELECT `username` FROM `$personTable` WHERE `personID` = ?";
				$resultEditor = sqlProcesses($sqlEditor, "s", [$editorID]);

				if (mysqli_num_rows($resultEditor) > 0) {
					//if result
					$editorUsername = mysqli_fetch_assoc($results)['username'];
					$metaDataArray['editorUsername'] = $editorUsername;
					
				}
			}
		}

		//Document meta data attribute initialized
		$this->documentObject->documentMetaDataObject = new DocumentMetaData($metaDataArray);

		//Prepare the meta data information to be Journal specific
		$this->documentObject->documentMetaDataObject->setMetaData("reviewDueDate", "");
		$this->documentObject->documentMetaDataObject->setMetaData("editDueDate", "");

		return $this->documentObject->documentMetaDataObject;		
	}

	public function setDocumentContent($dcArray)
	{
		//It's a journal, the Content should have been finalized
		echo "JournalState setDocumentContent(). <br>";
	}
	
	public function getDocumentContent($documentID)
	{
		echo "JournalState getDocumentContent(). <br>";

		foreach($this->documentObject->documentContent as $key => $value)
		{
			echo $key . " : ". $value . "<br>";	
		}		
	}

	public function setDocumentReviews($targetReviewObject, $attribute, $value)
	{
		//It's a journal, the Reviews should have been finalized
	}

	public function getDocumentReviews($documentID)
	{
		$sql = "SELECT * FROM `review` WHERE `documentID` = ?";

		$results = sqlProcesses($sql, "s", [$documentID]);

		$reviewsObjectArray = [];

		if(mysqli_num_rows($results) > 0)
		{
			while($individualReview = mysqli_fetch_assoc($results))
			{
				$individualReviewObject = new DocumentReview($individualReview);
				array_push($reviewsObjectArray, $individualReviewObject);
			}
		}

		return $reviewsObjectArray;
	}

	public function addDocumentReview(DocumentReview $review) {
		//empty
	}
}

?>