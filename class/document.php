<?php
require_once 'person.php';
require_once 'documentmetadata.php';

abstract class AbstractDocument 
{
	abstract function subscribe(Person $subscriber);
	abstract function unsubscribe(Person $subscriber);
	abstract function notify();
}

//Document class
class Document extends AbstractDocument 
{
	public $documentState;

	public $documentMetaData;
	public $documentContent;
	public $DocumentReviews = [];

	//Observer variable
	private $subscribers = array();

	//------------------------------------------------------------------ Functions
	//------------------------------------------------------------------ State functions
	public function __construct(DocumentState $documentState)
	{
		$this->transitionTo($documentState);
	}

	public function transitionTo(DocumentState $documentState): void
	{
		// echo "Document: Transition to " . get_class($documentState) . "<br>";
		$this->documentState = $documentState;
		$this->documentState->stateSetDocument($this);
	}

	public function concreteTransform(): void
	{
		$this->documentState->concreteTransform();
	}

	public function setDocumentMetaData($metadata)
	{
		$this->documentState->setDocumentMetaData($metadata);
	}

	public function getDocumentMetaData()
	{
		$this->documentState->getDocumentMetaData();
	}

	public function setDocumentContent($dcArray)
	{
		$this->documentState->setDocumentContent($dcArray);
	}
	
	public function getDocumentContent()
	{
		$this->documentState->getDocumentContent();
	}

	public function setDocumentReviews(Review $review)
	{
		$this->documentState->setDocumentReviews($review);
	}

	public function getDocumentReviews($reviewerIDArray)
	{
		$this->documentState->getDocumentReviews($reviewerIDArray);
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
abstract class DocumentState implements JsonSerializable
{
	protected $documentContext;

	public function stateSetDocument(Document $documentContext)
	{
		$this->documentContext = $documentContext;

		if($this->documentContext->getDocumentState() == "JournalState")
		{
			// echo "Document state is now ". $this->documentContext->getDocumentState() . "<br><br>";
			
			$this->documentContext->documentMetaData["printDate"] = "";
			$this->documentContext->documentMetaData["journalIssue"] = "";
		}
		else if($this->documentContext->getDocumentState() == "ManuscriptState")
		{
			// echo "Document state is now ". $this->documentContext->getDocumentState() . "<br><br>";

			unset($this->documentContext->documentMetaData["printDate"]);
			unset($this->documentContext->documentMetaData["journalIssue"]);
		}
	}

	public function jsonSerialize()
	{
		return get_class($this);
	}

	// abstract public function concreteTransform(): void;
	abstract public function setDocumentMetaData(DocumentMetadata $metadata);
	abstract public function getDocumentMetaData();

	abstract public function setDocumentContent($dcArray);
	abstract public function getDocumentContent();

	abstract public function setDocumentReviews(Review $drArray);
	abstract public function getDocumentReviews($reviewerIDArray);	
}

//-------------------------------------------------------------------------------------------------------- "Concrete" documents

//ManuscriptState class
class ManuscriptState extends DocumentState
{
	public function concreteTransform(): void
	{
		$this->documentContext->transitionTo(new JournalState());
	}

	public function setDocumentMetaData($dmdArray)
	{
		// print_r($dmdArray);
		foreach($dmdArray as $key => $value)
		{
			// echo $key . " : ". $value . "<br>";
			$this->documentContext->documentMetaData[$key] = $value;		
		}
	}

	public function getDocumentMetaData()
	{
		foreach($this->documentContext->documentMetaData as $key => $value)
		{
			// echo $key . " : ". $value . "<br>";	
		}		
	}

	public function setDocumentContent($dcArray)
	{
		foreach($dcArray as $key => $value)
		{
			$this->documentContext->documentContent[$key] = $value;		
			// echo $key . " : ". $value . "<br>";
		}	
	}
	
	public function getDocumentContent()
	{
		foreach($this->documentContext->documentContent as $key => $value)
		{
			// echo $key . " : ". $value . "<br>";	
		}	
	}

	//allow this function to take a reviewer object instead
	public function setDocumentReviews(Review $drArray)
	{
		$similarReviewers = false;

		if(sizeof($this->documentContext->DocumentReviews) > 0)
		{
			foreach($this->documentContext->DocumentReviews as $key => $value)
			{
				if($value->reviewerID == $drArray->reviewerID)
				{
					$similarReviewers = true;
					$this->documentContext->DocumentReviews[$key]["rating"]  = $drArray->rating;
					$this->documentContext->DocumentReviews[$key]["comment"]  = $drArray->comment;
					break;
				}
			}

			if($similarReviewers == false)
			{
				array_push($this->documentContext->DocumentReviews, $drArray);
			}
		}
		else
		{
			array_push($this->documentContext->DocumentReviews, $drArray);
		}
	}

	public function getDocumentReviews($reviewerIDArray)
	{
		foreach($reviewerIDArray as $targetReviewer)
		{

			foreach($this->documentContext->DocumentReviews as $key => $value)
			{
				if($targetReviewer == $value["reviewerID"])
				{
					// echo "Reviewer : " . $value["reviewerID"] . "<br>"; 
					// echo "rating : " . $value["rating"] . "<br>"; 
					// echo "comment : " . $value["comment"] . "<br><br>"; 
					break;
				}
				// else
				// {
				// 	echo "Dodging : " . $value["reviewerID"] . "<br><br>";	
				// }
			}
		}

	}
}

////JournalState class
class JournalState extends DocumentState
{
	public function concreteTransform(): void
	{
		$this->documentContext->transitionTo(new ManuscriptState());
	}

	public function setDocumentMetaData($dmdArray)
	{
		//It's a journal, the Non journal metadata should have been finalized
		//if the dmdArray's keys are not JournalIssue/printDate/Demote...chao from the scene
		//print_r(array_keys($dmdArray));
		// foreach($dmdArray as $key => $value)
		// {
		// 	if()
		// }
	}

	public function getDocumentMetaData()
	{
		foreach($this->documentContext->documentMetaData as $key => $value)
		{
		}
	}

	public function setDocumentContent($dcArray)
	{
		//It's a journal, the Content should have been finalized
	}
	
	public function getDocumentContent()
	{
		foreach($this->documentContext->documentContent as $key => $value)
		{
		}		
	}

	public function setDocumentReviews(Review $drArray)
	{
		//It's a journal, the Reviews should have been finalized
	}

	public function getDocumentReviews($reviewerIDArray)
	{		
		foreach($this->documentContext->DocumentReviews as $key => $value)
		{
		}
	}
}

?>