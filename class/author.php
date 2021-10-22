<?php
    require_once 'person.php';
    require_once 'document.php';

    class Author extends Person 
    {
        public $type = 1;

        public function getManuscript() {

        }

        public function setManuscript() {

        }

        public function updatePersonData() {
            
        }

        public function makePayment() {

        }
        
        //--------------------------------------------------------------------- Observer method
        public function update(AbstractDocument $documentObj) 
        {
            echo "Author notified<br>";
            $documentObj->getDocumentContent();
        }           
    }

?>