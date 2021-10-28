<?php
    require_once 'person.php';
    require_once 'document.php';

    class Author extends Person 
    {
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
    }

?>