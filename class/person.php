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

        abstract public function getManuscript();
        abstract public function setManuscript();
        abstract public function updatePersonData();
        abstract function update(AbstractDocument $documentObj);
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