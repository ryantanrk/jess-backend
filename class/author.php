<?php
    require_once 'person.php';
    require_once 'document.php';

    class Author extends Person 
    {
        // //------------------------------------------------------------------------------------ Singleton stuff above
        // private static $instances = [];
        // protected function __construct() { }
        // protected function __clone() { }
    
        // public static function getInstance(): Author
        // {
        //     $cls = static::class;
        //     if (!isset(self::$instances[$cls])) 
        //         self::$instances[$cls] = new static();
    
        //     return self::$instances[$cls];
        // }
        // //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Singleton stuff above
        
        public $type = 1;

        public function getManuscript(AbstractDocument $documentObj) 
        {
            $documentObj->getDocumentContent();
        }

        public function setManuscript(AbstractDocument $documentObj, $dcArray) 
        {
            $documentObj->setDocumentContent($dcArray);
        }

        public function makePayment() {

        }
        
        //--------------------------------------------------------------------- Observer method
        public function notify(AbstractDocument $documentObj) 
        {
            echo "Author notified<br>";
        }

        public function uploadNewDocument($doc) {
            $documentID = getNewID(3);
            $authorID = $doc['personID'];

            $title = $doc['title']; 
            $topic = $doc['topic']; 

            $documentToUpload = $doc['documentToUpload']["tmp_name"];
            $fileToUpload = file_get_contents($documentToUpload);        
            $authorRemarks = $doc['authorRemarks'];
            
            $dateOfSubmission = date("Y-m-d");

            $documentStatus = 'New';    
        
            $sql = "INSERT INTO `document`(
              `documentID`, `authorID`, `title`, `topic`, 
              `dateOfSubmission`, `file`, `authorRemarks`, `documentStatus`) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $paramVariablesArray = array(
                $documentID, $authorID, $title, $topic, 
                $dateOfSubmission, $fileToUpload, $authorRemarks, $documentStatus
            );

            sqlProcesses($sql, "ssssssss", $paramVariablesArray);
        }

        public function editDocument($documentID, $doc) {
            //$doc: doc object
        }
    }

?>