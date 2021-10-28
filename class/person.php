<?php
    require_once 'editor.php';
    require_once 'author.php';
    require_once 'reviewer.php';
    require_once 'document.php';
    
    abstract class Person {
        public $personID;
        public $username;
        public $password;
        public $email;
        public $dob;

        public function __construct($personID, $username, $password, $email, $dob) {
            //constructor
            $this->personID = $personID;
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
            $this->dob = $dob;
        }

        abstract public function getManuscript(AbstractDocument $documentObj);
        abstract public function setManuscript(AbstractDocument $documentObj, $someArray);
        abstract function notify(AbstractDocument $documentObj);
        
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
            return array($this->personID, $this->username, $this->password, $this->email, $this->dob);
        }              
    }

    // //------------------------------------------------------------------------------------------------ Testing zone
    // $DocumentObject = new Document(new ManuscriptState());
    // echo "Document object created <br><br>";

    // //---------------------------------------------------------------------------------------
    // $dmd = array(
    //     "documentID" => "doc1", 
    //     "title" => "docTitle1", 
    //     "topic" => "docTopic1", 
    //     "pages" => "docPages1", 
    //     "dateOfSubmission" => "docDateOfSubmission1", 
    //     "status" => "docStatus1", 
    //     "mainAuthor" => "docMainAuthor1", 
    //     "authorRemarks" => "docAuthorRemarks1", 
    //     "editorRemarks" => "docEditorRemarks1");

    // $DocumentObject->setDocumentMetaData($dmd);
    // echo "Document object meta data set <br><br>";

    // echo "<br><br>". "----------------------------------------------------------------------------------------" ."<br><br>";

    // $authorObject1 = new Author("AID1", "username", "password", "aid1@gmail.com", "1/1/1");
    // $authorObject2 = new Editor("EID2", "username1", "password1", "eid2@gmail.com", "2/2/2");

    // $DocumentObject->subscribe($authorObject1);
    // $DocumentObject->subscribe($authorObject2);
    // $DocumentObject->notify();    
?>