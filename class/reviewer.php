<?php
    require_once 'person.php';
    require_once 'document.php';

    class Reviewer extends Person {
        public $type = 2;

        public function getManuscript(AbstractDocument $documentObj) 
        {
            $documentObj->getDocumentMetaData();
            $documentObj->getDocumentContent();
            $documentObj->getDocumentReviews();
        }

        public function setManuscript(AbstractDocument $documentObj, $drArray) {

        }

        //--------------------------------------------------------------------- Observer method
        public function notify(AbstractDocument $documentObj) 
        {
            echo "Reviewer notified<br>";
        }

    }
?>