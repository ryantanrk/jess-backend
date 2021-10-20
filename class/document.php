<?php
/*
---------
Person
----------
Username
Password
Email
Age

//Reviewers only get 1 area of expertise 

---------
Document
---------
ID
Title
Topic
Pages
Submit Date
Status
Author name
Author Remarks
Editor comments
Attachments
*/

//Document class
class Document
{
	private $documentState;

	public $documentMetaData=array("documentID"=>"",
							"title"=>"",
							"topic"=>"",
							"pages"=>"",
							"dateOfSubmission"=>"",
							"status"=>"",
							"mainAuthors"=>"",
							"authorRemarks"=>"",
							"editorRemarks"=>""
						);

	public $documentContent = array("fileContent"=>"",
							"pdfFile"=>""
						);

	public $documentReview = array("tenPointRating"=>"",
							"reviewerComments"=>"",
							"reviewerID"=>""
						);

	public $peopleInvolved = array();

	//------------------------------------------------------------------ Functions
	//State functions
	public function __construct(DocumentState $documentState)
	{
		$this->transitionTo($documentState);
	}

	public function transitionTo(DocumentState $documentState): void
	{
		echo "Document: Transition to " . get_class($documentState) . "<br>";
		$this->documentState = $documentState;
		$this->documentState->stateSetDocument($this);
	}

	public function concreteTransform(): void
	{
		$this->documentState->concreteTransform();
	}

	public function setDocumentMetaData()
	{
		$this->documentState->setDocumentMetaData();
	}

	public function getDocumentMetaData()
	{
		$this->documentState->getDocumentMetaData();
	}

	public function setDocumentContent()
	{
		$this->documentState->setDocumentContent();
	}
	
	public function getDocumentContent()
	{
		$this->documentState->getDocumentContent();
	}

	public function setDocumentReview()
	{
		$this->documentState->setDocumentReview();
	}

	public function getDocumentReview()
	{
		$this->documentState->getDocumentReview();
	}

	public function getDocumentState() : string
	{
		return get_class($this->documentState);
	}

	//Observer function
	public function subscribe()
	{

	}

	public function unSubscribe()
	{
		
	}
	public function notifySubscribers()
	{
		
	}
}

//DocumentState class
abstract class DocumentState 
{
	protected $documentContext;

	public function stateSetDocument(Document $documentContext)
	{
		$this->documentContext = $documentContext;

		if($this->documentContext->getDocumentState() == "JournalState")
		{
			echo "Document state is now ". $this->documentContext->getDocumentState() . "<br><br>";

			$this->documentContext->documentMetaData["printDate"] = "";
			$this->documentContext->documentMetaData["journalIssue"] = "";

			print_r(array_keys($this->documentContext->documentMetaData));
			echo "<br><br>";
		}
	}

	abstract public function concreteTransform(): void;
	abstract public function setDocumentMetaData();
	abstract public function getDocumentMetaData();

	abstract public function setDocumentContent();
	abstract public function getDocumentContent();

	abstract public function setDocumentReview();
	abstract public function getDocumentReview();	
}

//ManuscriptState class
class ManuscriptState extends DocumentState
{
	public function concreteTransform(): void
	{
		echo "ManuscriptState transform to JournalState via the state's document object's transition function. <br>";
		$this->documentContext->transitionTo(new JournalState());
	}

	public function setDocumentMetaData()
	{
		echo "ManuscriptState setDocumentMetaData(). <br>";
	}

	public function getDocumentMetaData()
	{
		echo "ManuscriptState getDocumentMetaData(). <br>";
	}

	public function setDocumentContent()
	{
		echo "ManuscriptState setDocumentContent(). <br>";
	}
	
	public function getDocumentContent()
	{
		echo "ManuscriptState getDocumentContent(). <br>";
	}

	public function setDocumentReview()
	{
		echo "ManuscriptState setDocumentReview(). <br>";
	}

	public function getDocumentReview()
	{
		echo "ManuscriptState getDocumentReview(). <br>";
	}
}

////JournalState class
class JournalState extends DocumentState
{
	public function concreteTransform(): void
	{
		echo "JournalState transform to Manuscript via the state's document object's transition function. <br>";
		$this->documentContext->transitionTo(new ManuscriptState());
	}

	public function setDocumentMetaData()
	{
		echo "JournalState setDocumentMetaData(). <br>";
	}

	public function getDocumentMetaData()
	{
		echo "JournalState getDocumentMetaData(). <br>";
	}

	public function setDocumentContent()
	{
		echo "JournalState setDocumentContent(). <br>";
	}
	
	public function getDocumentContent()
	{
		echo "JournalState getDocumentContent(). <br>";
	}

	public function setDocumentReview()
	{
		echo "JournalState setDocumentReview(). <br>";
	}

	public function getDocumentReview()
	{
		echo "JournalState getDocumentReview(). <br>";
	}
}

// ------------------------------------------------------------------------------------------------ The client code.

$context = new Document(new ManuscriptState());
echo "Dah <br><br>";

$context->concreteTransform();
$context->setDocumentReview();
$context->getDocumentReview();
echo "Dah <br><br>";

$context->concreteTransform();
$context->setDocumentReview();
$context->getDocumentReview();
echo "Dah <br><br>";
?>