<?php
require_once 'person.php';

abstract class DocumentAttributes{}

class DocumentMetaData extends DocumentAttributes
{
	public $documentID;
	public $authorID;
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

	//Initialize the attributes on creation. Can initialize [everything or somethings(all keys must be there)] or [nothing(empty array)]
	function __construct($metaDataArray)
	{
		if(sizeof($metaDataArray) != 0)
		{
			$this->documentID = $metaDataArray["documentID"];
			$this->authorID = $metaDataArray["authorID"];
			$this->editorID = $metaDataArray["editorID"];
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
		sqlProcesses("UPDATE `document` SET `{$attribute}` = ? WHERE `documentID`= ?", "ss", [$value, $this->documentID]);
		
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
	}

	public function getMetaData()
	{
		$metaDataArray = array("documentID" => $this->documentID, "authorID" => $this->authorID, "editorID" => $this->editorID, 
								"title" => $this->title, "topic" => $this->topic, "dateOfSubmission" => $this->dateOfSubmission, 
								"printDate" => $this->printDate, "authorRemarks" => $this->authorRemarks, 
								"editorRemarks" => $this->editorRemarks, "reviewDueDate" => $this->reviewDueDate, 
								"editDueDate" => $this->editDueDate, "price" => $this->price, "journalIssue" => $this->journalIssue, 
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

	//Initialize the attributes on creation. Can initialize [everything or somethings(all keys must be there)] or [nothing(empty array)]
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
		sqlProcesses("UPDATE `review` SET `{$attribute}` = ? WHERE `documentID`= ? AND `reviewerID` = ?", "sss", 
					[$value, $this->documentID, $this->reviewerID]);

		if($attribute == "documentID") 
			$this->documentID = $value;
		
		else if($attribute == "reviewerID") 
			$this->reviewerID = $value;	

		else if($attribute == "rating") 
			$this->rating = $value;
		
		else if($attribute == "comment") 
			$this->comment = $value;	

		else if($attribute == "reviewStatus") 
			$this->reviewStatus = $value;
		
		else if($attribute == "dateOfReviewCompletion") 
			$this->dateOfReviewCompletion = $value;
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
	protected $documentStateObject;

	//------------------------------------------------------------------ State functions

	//Documents are initialized as their respective states but EMPTY
	public function __construct(DocumentState $documentStateObject){
		$this->documentStateObject = $documentStateObject;
		$this->documentStateObject->stateSetDocument($this);
	}

	//Set methods are according to their states
	public function setDocumentMetaData($attribute, $value){
		$this->documentStateObject->setDocumentMetaData($attribute, $value);
	}
	
	//Set methods are according to their states
	public function setDocumentReview($reviewerID, $attribute, $value){
		$this->documentStateObject->setDocumentReview($reviewerID, $attribute, $value);
	}

	//Get methods are according to their states
	public function getDocumentMetaData(){
		return $this->documentStateObject->getDocumentMetaData();
	}
	
	//Get methods are according to their states
	public function getDocumentReviews(){
		return $this->documentStateObject->getDocumentReviews();
	}

	public function getDocumentContent(){
		return $this->documentStateObject->getDocumentContent();
	}
}

//DocumentState class has a DocumentObject
abstract class DocumentState
{
	protected $documentObject;
	protected $documentMetaDataObject;
	protected $documentReviewsArray = array();

	public function __construct($documentID)
	{
		//initialize document attributes
		if($documentID != "")
		{
			//split temp variable
			$metaDataResults = sqlProcesses("SELECT * FROM `document` WHERE `documentID` = ?", "s", [$documentID]);
			$metaDataRow = mysqli_fetch_assoc($metaDataResults);
			$this->documentMetaDataObject = new DocumentMetaData($metaDataRow);
			$this->documentReviewsArray = [];

			$allReviews = sqlProcesses("SELECT * FROM `review` WHERE `documentID` = ?", "s", [$documentID]);

			if(mysqli_num_rows($allReviews) > 0)
			{
				$reviews = [];
				while($individualReview = mysqli_fetch_assoc($allReviews))
				{
					$individualReviewObject = new DocumentReview($individualReview);
					array_push($reviews, $individualReviewObject);
				}
				
				$this->documentReviewsArray = $reviews;
			}
		}
	}

	public function stateSetDocument(Document $documentObject){$this->documentObject = $documentObject;}

	//Set methods are according to their states
	abstract public function setDocumentMetaData($attribute, $value);
	abstract public function setDocumentReview($reviewerID, $attribute, $value);

	//Get methods are according to their states
	abstract public function getDocumentMetaData();
	abstract public function getDocumentReviews();

	public function getDocumentContent() {
        global $arr;
        //get title & file
        $query = "SELECT `title`, `file` FROM `document` WHERE documentID = ?";
        $result = sqlProcesses($query, "s", [$this->getDocumentMetaData()->documentID]);

        //if result found
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $title = $row['title'];
                $file = $row['file'];
            }
            //set headers
            header("Content-Type: application/pdf");
            header('Content-Disposition: inline; filename="' . $title . '.pdf"');
            header("Content-Transfer-Encoding: binary");
            header('Accept-Ranges: bytes');

            //determine filename
            $filename = $title . ".pdf";

            //create file and write to it
            $myfile = fopen("../documents/" . $filename, "w") or die("Unable to open file!");
            fwrite($myfile, $file);
            fclose($myfile);

            //go to file
            header('Location: ' . "../documents/" . $filename, true, 302);
        }
        else {
            $arr = ["error" => "document not found"];
        }
    }
}

//-------------------------------------------------------------------------------------------------------- "Concrete" documents

//ManuscriptState class
//Get methods good
//Prepare set methods 
class ManuscriptState extends DocumentState
{
	//Journal related information should not be set here
	public function setDocumentMetaData($attribute, $value)
	{
		if($attribute != "printDate" || $attribute != "journalIssue")
			$this->documentMetaDataObject->setMetaData($attribute, $value);
	}

	//Reviews can be set when a document is a manuscript
	public function setDocumentReview($reviewerID, $attribute, $value)
	{
		foreach($this->documentReviewsArray as $individualReviewObject)
		{
			$individualReviewArray = $individualReviewObject->getReviewData();

			if($individualReviewArray["reviewerID"] == $reviewerID)
				$individualReviewObject->setReviewData($attribute, $value);
		}
	}

	//Return only manuscript related metadata
	public function getDocumentMetaData()
	{
		return $this->documentMetaDataObject;
	}

	//Return all reviews as review objects
	public function getDocumentReviews()
	{
		return $this->documentReviewsArray;
	}
}

//JournalState class
class JournalState extends DocumentState
{
	//Only Journal related information can be set here
	public function setDocumentMetaData($attribute, $value)
	{
		if($attribute == "journalIssue" || $attribute == "printDate")
			$this->documentMetaDataObject->setMetaData($attribute, $value);
	}

	//Reviews cannot be set when a document is a journal
	public function setDocumentReview($reviewerID, $attribute, $value){}

	//Return only journal related metadata
	public function getDocumentMetaData()
	{
		$this->documentMetaDataObject->authorRemarks = "";
		$this->documentMetaDataObject->editorRemarks = "";
		$this->documentMetaDataObject->reviewDueDate = "";
		$this->documentMetaDataObject->editDueDate = "";

		return $this->documentMetaDataObject;
	}

	//Return all reviews as review objects
	public function getDocumentReviews()
	{
		return $this->documentReviewsArray;
	}
}
?>