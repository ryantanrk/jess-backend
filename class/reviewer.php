<?php
    require_once 'person.php';
    require_once 'document.php';

    class Reviewer extends Person {
        public $type = 2;

        public function getManuscript() {

        }

        public function setManuscript() {

        }

        public function updatePersonData() {

        }

        //--------------------------------------------------------------------- Observer method
        public function update(AbstractDocument $documentObj) 
        {
            echo "Reviewer notified<br>";
            $documentObj->getDocumentReviews();
        }           
    }
?>