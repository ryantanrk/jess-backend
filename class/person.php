<?php
    require_once 'document.php';
    
    abstract class Person 
    {
        public $personID;
        public $username;
        public $password;
        public $email;
        public $dob;
        public $type;

        abstract public function getDocument($documentID);
        abstract public function setDocument($documentObject, $targetAttribute, $value);

        public function updatePersonData($newID, $newUserName, $newPassword, $newEmail, $newDob) 
        {
            $this->personID = $newID;
            $this->username = $newUserName;
            $this->password = $newPassword;
            $this->email = $newEmail;
            $this->dob = $newDob;
        }

        public function getPersonData()
        {
            return array("PersonID" => $this->personID, "Username" => $this->username, "Password" => $this->password, "Email" => $this->email, "DOB" => $this->dob);
        }              
    }

    class Editor extends Person 
    {
        public $type = 0;
        protected function __construct() { }

        public function getDocument($documentID) 
        {
            $documentObj->setDocumentMetaData($objectMetaData, $attribute, $value);
            $documentObj->getDocumentReviews();
        }

        public function setDocument($documentObject, $targetAttribute, $value) 
        {
            $documentObj->setDocumentMetaData($dmdArray);
        }
    }

    class Reviewer extends Person 
    {
        public $type = 2;
        //------------------------------------------------------------------------------------ Singleton stuff above
        private static $instances = [];
        public function __construct() { }
        protected function __clone() { }
    
        public static function getInstance(): Reviewer
        {
            $cls = static::class;
            if (!isset(self::$instances[$cls])) 
                self::$instances[$cls] = new static();
    
            return self::$instances[$cls];
        }
        //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Singleton stuff above

        public $areaOfExpertise;
        public $status;

        public function getDocument($documentIDReviewerID) 
        {
        	$documentObject = new Document(new ManuscriptState);

        	$tempArray = explode("-", $documentIDReviewerID);     	

            $reviewsObjectArray = $documentObject->getDocumentReviews($tempArray[0]);

            writeLine("Reviewer zone");
            writeLine($documentIDReviewerID);

            foreach($reviewsObjectArray as $reviewObject)
            {
            	$reviewerDataArray = $reviewObject->getReviewData();

            	if($reviewerDataArray["reviewerID"] == $tempArray[1])
	            	return $reviewObject;
            }

            return new DocumentReview([]);
        }

        public function setDocument($documentReviewObject, $targetAttribute, $value) 
        {
            $documentReviewObject->setReviewData($targetAttribute, $value);
        }
    }

    class Author extends Person 
    {
        public $type = 1;
        //------------------------------------------------------------------------------------ Singleton stuff above
        private static $instances = [];
        public function __construct() { }
        protected function __clone() { }
    
        public static function getInstance(): Author
        {
            $cls = static::class;
            if (!isset(self::$instances[$cls])) 
                self::$instances[$cls] = new static();
    
            return self::$instances[$cls];
        }
        //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Singleton stuff above

        public function getDocument($documentID) 
        {
            //Find out if we are dealing with a manuscript or a Journal
            $result = sqlProcesses("SELECT `documentStatus` FROM `document` WHERE `documentID` = ?", "s", [$documentID]);
            $unSQLedObject = mysqli_fetch_assoc($result);

            $documentObject = "";

            //discernment
            if($unSQLedObject['documentStatus'] != "published")
                $documentObject = new Document(new ManuscriptState);
            else
                $documentObject = new Document(new JournalState);

            //State specific data returned
            $metaDataObject = $documentObject->getDocumentMetaData($documentID);

            return $metaDataObject;
        }

        public function setDocument($documentMetaDataObject, $targetAttribute, $value) 
        {
            if($targetAttribute == "documentStatus" || $targetAttribute == "authorRemarks")
            {
                $documentMetaDataObject->setMetaData($targetAttribute, $value);
            }
            else if($targetAttribute == "file")
            {
                $tempArray = $documentMetaDataObject->getMetaData();
                $fileTempName = $value;
                $fileToUpload = file_get_contents($fileTempName);
                sqlProcesses("UPDATE `document` SET `file`=? WHERE `documentID`=?", "ss",[$fileToUpload, $tempArray["documentID"]]);
            }
        }

        public function uploadNewDocument($documentObject, $value)
        {
            $authorID = $value['personID'];
            $title = $value['title']; 
            $topicOption = $value['topicOption']; 

            $documentToUpload = $value['documentToUpload'];
            $fileTempName = $value["documentToUpload"]["tmp_name"];
            $fileToUpload = file_get_contents($fileTempName);

            $authorRemarks = $value['authorRemarks'];

            $documentID = sqlProcesses("SELECT COUNT(?) AS TOTALDOCS FROM `document`", "s", ['*']);
            $documentID = mysqli_fetch_assoc($documentID);
            $documentID = 'D' . ($documentID['TOTALDOCS'] + 1);
            
            $editorID = NULL;
            
            $dateOfSubmission = date("Y-m-d");
            $printDate = '';
            
            $editorRemarks = ''; 
            $reviewDueDate = '';    
        
            $editDueDate = '';
            $price = '';
            $journalIssue = '';
            $documentStatus = 'new';    
        
            $sql = "INSERT INTO `document`(
              `documentID`, `authorID`, `editorID`, `title`, `topic`, 
              `dateOfSubmission`, `file`, `authorRemarks`, `documentStatus`) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
              $paramVariablesArray = array(
                $documentID, $authorID, $editorID, $title, $topicOption, 
                $dateOfSubmission, $fileToUpload, $authorRemarks, $documentStatus        
            );

            sqlProcesses($sql, "sssssssss", $paramVariablesArray);                     
        }
    }    
?>