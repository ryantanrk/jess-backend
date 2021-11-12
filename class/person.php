<?php
    require_once 'document.php';
    require_once 'factory.php';
    
    abstract class Person 
    {
        protected $personID;
        protected $username;
        protected $password;
        protected $email;
        protected $dob;

        //Different for each type of person
        abstract public function getAuthorizedDocumentAttribute($documentID);
        
        //Different for each type of person
        abstract public function setAuthorizedDocumentAttribute($documentObject, $targetAttribute, $value);

        //Want to update this to the array's ALL OR NOTHING technique?
        public function updatePersonData($newID, $newUserName, $newPassword, $newEmail, $newDob) 
        {
            $this->personID = $newID;
            $this->username = $newUserName;
            $this->password = $newPassword;
            $this->email = $newEmail;
            $this->dob = $newDob;
        }

        //Get personData
        public function getPersonData()
        {
            return array(
            		"personID" => $this->personID, 
            		"username" => $this->username, 
            		"password" => $this->password, 
            		"email" => $this->email, 
            		"dob" => $this->dob
            	);
        }
    }

    class Editor extends Person 
    {
        //Enter document ID, retrieve document's metadata & all reviews
        public function getAuthorizedDocumentAttribute($documentID) 
        {
            //Find out if we are dealing with a manuscript or a Journal
            $documentObject = retrieveDocumentFromDatabaseInCorrectState($documentID);

            //Prepare document to contain only authorized and appropriate meta data
            // $metaDataObject = $documentObject->getDocumentMetaData($documentID);
            // $reviewsObject = $documentObject->getDocumentReviews($documentID);

            // return [$metaDataObject, $reviewsObject];

            // replace temp with query
            return [$documentObject->getDocumentMetaData($documentID), $documentObject->getDocumentReviews($documentID)];
        }

		//Editor only edits editor authorized information
        public function setAuthorizedDocumentAttribute($documentID, $targetAttribute, $value) 
        {
            //Find out if we are dealing with a manuscript or a Journal
            $documentObject = retrieveDocumentFromDatabaseInCorrectState($documentID);

            //decompose conditional
			if ($this->authorizedAttribute($targetAttribute))
                $documentObject->setDocumentMetaData($targetAttribute, $value);
        }

        //decompose conditional, for use in setAuthorizedDocumentAttribute
        public function authorizedAttribute($targetAttribute)
        {
            //targetAttributes will store strings of all the attribute names
            $targetAttributes = [
                "documentID", "editorID", "printDate", "editorRemarks", "reviewDueDate", "editDueDate", 
                "price", "journalIssue", "documentStatus"
            ];

            //returns a boolean
            return in_array($targetAttribute, $targetAttributes);
        }

        //All good
        public function setAuthorizedReviewAttribute($documentIDReviewerID, $targetAttribute, $value) 
        {
            if($targetAttribute != "createNewReviewRequest")
            {
                $tempArray = explode("-",$documentIDReviewerID);
                
                //Find out if we are dealing with a manuscript or a Journal
                $documentObject = retrieveDocumentFromDatabaseInCorrectState($tempArray[0]);           

    			if($targetAttribute == "documentID" || $targetAttribute == "reviewerID" || $targetAttribute == "reviewStatus")
                {
                    $totalReviewsArray = $documentObject->getDocumentReviews();

                    foreach($totalReviewsArray as $individualReviewObject)
                    {
                        //split temporary variable
                        $reviewerdata = $individualReviewObject->getReviewData();
                        $reviewerID = $reviewerdata["reviewerID"];

                        if($reviewerID == $tempArray[1])			    
                            $individualReviewObject->setReviewData($targetAttribute, $value);
                    }
                }
            }
			else
			{
				$tempArray = explode("-", $value);

				sqlProcesses("INSERT INTO `review`(`documentID`, `reviewerID`, `reviewStatus`) 
				VALUES (?, ?, ?)", "sss", [$tempArray[0], $tempArray[1], "pending"]);

                //set review due date
                $this->setAuthorizedDocumentAttribute($tempArray[0], "reviewDueDate", date("Y-m-d", strtotime("+30 days")));

                //notify reviewer
                //get reviewer data
                $reviewer = getPersonFromID($tempArray[1]);
                $reviewerdata = $reviewer->getPersonData();

                //get document data
                $document = retrieveDocumentFromDatabaseInCorrectState($tempArray[0]);
                $metadata = $document->getDocumentMetaData();

                //set message
                $message = "Hello " . $reviewerdata['username'] . ",<br/><br/>";
                $message .= "You have been assigned to review document \"" . $metadata->title . "\".<br/>";
                $message .= "Please do so within 30 days.<br/><br/>";
                $message .= "<b>JESS</b><br/>";
                $message .= "<i>This is an automatically generated email.</i>";

                //send email to reviewer
                $this->notify($reviewerdata['personID'], "Pending Review: " . $metadata->title, $message);
			}
        }

        //approve reviewer
        public function approveReviewer($reviewerID) {
            global $arr;
            sqlProcesses("UPDATE `reviewerspecific` SET `status` = ? WHERE `personID` = ?",
                "ss", ["available", $reviewerID]);

            $arr = ["message" => "successfully approved " . $reviewerID];
        }

        //notify person using email
        public function notify($personID, $subject, $message) {
            global $email, $arr;
            //get person object
            $personobj = getPersonFromID($personID);
            $persondata = $personobj->getPersonData();

            $headers = "From: JESS <" . $email . ">" . PHP_EOL;
            $headers .= "MIME-Version: 1.0" . PHP_EOL;
            $headers .= "Content-Type: text/html; charset=UTF-8" . PHP_EOL;
            //email function
            if (mail($persondata['email'], $subject, $message, $headers)) {
                $arr['email'] = "email sent to " . $persondata['email'];
            }
            else {
                $arr['email'] = "email not sent to " . $persondata['email'];
            }
        }
    }

    //Prepare Reviewer to go through a state
    class Reviewer extends Person 
    {
        public $areaOfExpertise;
        public $status;

        //Looking good
        public function getAuthorizedDocumentAttribute($documentID) 
        {
            $documentObject = retrieveDocumentFromDatabaseInCorrectState($documentID);     	

            $reviewsArray = $documentObject->getDocumentReviews($documentID);

            foreach($reviewsArray as $reviewObject)
            {
                $reviewerID = $reviewObject->getReviewData();
                $reviewerID = $reviewerID["reviewerID"];

                if($reviewerID == $this->personID)
                    return $reviewObject;
            }
        }

        public function setAuthorizedDocumentAttribute($documentID, $targetAttribute, $value)
        {
            $documentObject = retrieveDocumentFromDatabaseInCorrectState($documentID);

            $documentObject->setDocumentReview($this->personID, $targetAttribute, $value);
        }

        public function setReviewerStatus($value) {
            global $arr;
            $this->status = $value;

            $allowedValues = ["available", "on leave", "occupied"];

            if (in_array($value, $allowedValues)) {
                sqlProcesses("UPDATE `reviewerspecific` SET `status` = ? WHERE `personID` = ?", 
                        "ss", [$value, $this->personID]);

                $arr = ["message" => "change success: " . $this->personID];
            }
            else {
                $arr = ["error" => "unable to change status to: " . $value];
            }
        }

        public function getPersonData()
        {
            return array(
            		"personID" => $this->personID, 
            		"username" => $this->username, 
            		"password" => $this->password, 
            		"email" => $this->email, 
            		"dob" => $this->dob,
                    "areaOfExpertise" => $this->areaOfExpertise,
                    "status" => $this->status
            	);
        }
    }

    //Prepare Author to go through a state
    class Author extends Person 
    {
        //Looking good
        public function getAuthorizedDocumentAttribute($documentID) 
        {
            //Find out if we are dealing with a manuscript or a Journal
            $documentObject = retrieveDocumentFromDatabaseInCorrectState($documentID);

            //State specific data returned
            // $metaDataObject = $documentObject->getDocumentMetaData($documentID);

            // return $metaDataObject;
            // replace temp with query
            return $documentObject->getDocumentMetaData($documentID);
        }

        //Prepare to go through a state
        public function setAuthorizedDocumentAttribute($documentID, $targetAttribute, $value) 
        {
            //Find out if we are dealing with a manuscript or a Journal
            $documentObject = retrieveDocumentFromDatabaseInCorrectState($documentID); 

            if($targetAttribute == "documentStatus" || $targetAttribute == "authorRemarks")
            {
                $documentObject->getDocumentMetaData()->setMetaData($targetAttribute, $value);
            }
            else if($targetAttribute == "file")
            {
                //Inline temp
                $fileToUpload = file_get_contents($value);                  
                sqlProcesses("UPDATE `document` SET `file`=? WHERE `documentID`=?", "ss",[$fileToUpload, $documentID]);
            }
        }

        //Dah puas
        public function uploadNewDocument($value)
        {
            global $arr;
            $authorID = $value['authorID'];
            $title = $value['title']; 
            $topicOption = $value['topic']; 

            $fileToUpload = file_get_contents($value['documentToUpload']);

            $authorRemarks = $value['authorRemarks'];

            //Split Temporary Variable
            $documentIDSQLObject = sqlProcesses("SELECT COUNT(?) AS TOTALDOCS FROM `document`", "s", ['*']);
            $documentIDSQLArray = mysqli_fetch_assoc($documentIDSQLObject);
            $documentID = 'D' . ($documentIDSQLArray['TOTALDOCS'] + 1);

            $editorID = NULL;

            $dateOfSubmission = date("Y-m-d");

            $documentStatus = 'new';

        	//---------------------------------------------------------------------- The potentially move to document area stuff
            $sql = "INSERT INTO `document`(
              `documentID`, `authorID`, `editorID`, `title`, `topic`, 
              `dateOfSubmission`, `file`, `authorRemarks`, `documentStatus`) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
              $paramVariablesArray = array(
                $documentID, $authorID, $editorID, $title, $topicOption, 
                $dateOfSubmission, $fileToUpload, $authorRemarks, $documentStatus        
            );

            sqlProcesses($sql, "sssssssss", $paramVariablesArray);
            $arr = ["message" => "document upload successfully"];
            //----------------------------------------------------------------------                  
        }
    }    
?>