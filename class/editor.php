<?php
    require_once 'person.php';
    require_once 'document.php';

    class Editor extends Person {
        public $type = 0;

        public function getManuscript(AbstractDocument $documentObj) 
        {
            $documentObj->getDocumentMetaData();
            $documentObj->getDocumentContent();
            $documentObj->getDocumentReviews();
        }

        public function setManuscript(AbstractDocument $documentObj, $dmdArray) 
        {
            $documentObj->setDocumentMetaData($dmdArray);
        }

        //--------------------------------------------------------------------- Observer method
        public function notify(AbstractDocument $documentObj) 
        {
          echo "Editor notified<br>";
        }           
    }
?>