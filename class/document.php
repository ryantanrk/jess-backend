<?php
require_once 'person.php';

abstract class AbstractDocument 
{
	abstract function subscribe(Person $subscriber);
	abstract function unsubscribe(Person $subscriber);
	abstract function notify();
}

//Document class
class Document extends AbstractDocument 
{
	private $documentState;

	public $documentMetaData=array("documentID"=>"",
							"title"=>"",
							"topic"=>"",
							"pages"=>"",
							"dateOfSubmission"=>"",
							"status"=>"",
							"mainAuthor"=>"",
							"authorRemarks"=>"",
							"editorRemarks"=>""
						);

	public $documentContent = array("fileContent"=>"",
							"pdfFile"=>""
						);

	public $DocumentReviews = array();

	private $subscribers = array();

	//------------------------------------------------------------------ Functions
	//------------------------------------------------------------------ State functions
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

	public function setDocumentMetaData($dmdArray)
	{
		$this->documentState->setDocumentMetaData($dmdArray);
	}

	public function getDocumentMetaData()
	{
		$this->documentState->getDocumentMetaData();
	}

	public function setDocumentContent($dcArray)
	{
		$this->documentState->setDocumentContent();
	}
	
	public function getDocumentContent()
	{
		$this->documentState->getDocumentContent();
	}

	public function setDocumentReviews($drArray)
	{
		$this->documentState->setDocumentReviews($drArray);
	}

	public function getDocumentReviews()
	{
		$this->documentState->getDocumentReviews();
	}

	public function getDocumentState() : string
	{
		return get_class($this->documentState);
	}

	//------------------------------------------------------------------ Observer functions
	function subscribe(Person $subscriber) 
	{
		array_push($this->subscribers, $subscriber);
		// print_r($this->subscribers);
	}

	function unsubscribe(Person $subscriber) 
	{
		//$key = array_search($observer_in, $this->subscribers);
		foreach($this->subscribers as $okey => $oval) 
		{
			if ($oval == $observer_in)  
			unset($this->subscribers[$okey]);
		}
	}

	function notify() 
	{
		foreach($this->subscribers as $obs) 
		{
			$obs->update($this);
		}
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
		else if($this->documentContext->getDocumentState() == "ManuscriptState")
		{
			echo "Document state is now ". $this->documentContext->getDocumentState() . "<br><br>";

			unset($this->documentContext->documentMetaData["printDate"]);
			unset($this->documentContext->documentMetaData["journalIssue"]);

			print_r(array_keys($this->documentContext->documentMetaData));
			echo "<br><br>";
		}
	}

	abstract public function concreteTransform(): void;
	abstract public function setDocumentMetaData($dmdArray);
	abstract public function getDocumentMetaData();

	abstract public function setDocumentContent($dcArray);
	abstract public function getDocumentContent();

	abstract public function setDocumentReviews($drArray);
	abstract public function getDocumentReviews();	
}

//-------------------------------------------------------------------------------------------------------- "Concrete" documents

//ManuscriptState class
class ManuscriptState extends DocumentState
{
	public function concreteTransform(): void
	{
		echo "ManuscriptState transform to JournalState via the state's document object's transition function. <br>";
		$this->documentContext->transitionTo(new JournalState());
	}

	public function setDocumentMetaData($dmdArray)
	{
		echo "ManuscriptState setDocumentMetaData(). <br>";

		// print_r($dmdArray);
		foreach($dmdArray as $key => $value)
		{
			// echo $key . " : ". $value . "<br>";
			$this->documentContext->documentMetaData[$key] = $value;		
		}
	}

	public function getDocumentMetaData()
	{
		echo "ManuscriptState getDocumentMetaData(). <br>";

		foreach($this->documentContext->documentMetaData as $key => $value)
		{
			echo $key . " : ". $value . "<br>";	
		}		
	}

	public function setDocumentContent($dcArray)
	{
		echo "ManuscriptState setDocumentContent(). <br>";

		foreach($dcArray as $key => $value)
		{
			$this->documentContext->documentContext[$key] = $value;		
		}	
	}
	
	public function getDocumentContent()
	{
		echo "ManuscriptState getDocumentContent(). <br>";

		foreach($this->documentContext->documentContent as $key => $value)
		{
			echo $key . " : ". $value . "<br>";	
		}	
	}

	public function setDocumentReviews($drArray)
	{
		echo "ManuscriptState setDocumentReviews(). <br>";

		$similarReviewers = false;

		foreach($this->documentContext->DocumentReviews as $key => $value)
		{
			if($value["reviewer"] == $drArray["reviewer"])
			{
				// echo $value["reviewer"] . " = " . $drArray["reviewer"] . "<br>";
				$similarReviewers = true;
				break;
			}
		}

		if($similarReviewers == false)
		{
			array_push($this->documentContext->DocumentReviews, $drArray);
		}
	}

	public function getDocumentReviews()
	{
		echo "ManuscriptState getDocumentReviews(). <br>";

		foreach($this->documentContext->DocumentReviews as $key => $value)
		{
			echo "Reviewer : " . $value["reviewer"] . "<br>"; 
			echo "rating : " . $value["rating"] . "<br>"; 
			echo "comments : " . $value["comments"] . "<br><br>"; 
		}
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

	public function setDocumentMetaData($dmdArray)
	{
		//It's a journal, the Non journal metadata should have been finalized
		echo "JournalState setDocumentMetaData(). <br>";



	}

	public function getDocumentMetaData()
	{
		echo "JournalState getDocumentMetaData(). <br>";

		foreach($this->documentContext->documentMetaData as $key => $value)
		{
			echo $key . " : " . $value . "<br>";
		}
	}

	public function setDocumentContent($dcArray)
	{
		//It's a journal, the Content should have been finalized
		echo "JournalState setDocumentContent(). <br>";
	}
	
	public function getDocumentContent()
	{
		echo "JournalState getDocumentContent(). <br>";

		foreach($this->documentContext->documentContent as $key => $value)
		{
			echo $key . " : ". $value . "<br>";	
		}		
	}

	public function setDocumentReviews($drArray)
	{
		//It's a journal, the Reviews should have been finalized
		echo "JournalState setDocumentReviews(). <br>";
	}

	public function getDocumentReviews()
	{
		echo "JournalState getDocumentReviews(). <br>";	
		
		foreach($this->documentContext->DocumentReviews as $key => $value)
		{
			echo "Reviewer : " . $value["reviewer"] . "<br>"; 
			echo "rating : " . $value["rating"] . "<br>"; 
			echo "comments : " . $value["comments"] . "<br><br>"; 
		}
	}
}

?>