<?php
    require_once 'person.php';
    require_once 'document.php';

    class Editor extends Person {
        public $type = 0;

        public function getManuscript() {

        }

        public function setManuscript() {

        }

        public function updatePersonData() {
            
        }

        //--------------------------------------------------------------------- Observer method
        public function update(AbstractDocument $documentObj) 
        {
          echo "Editor notified<br>";
          $documentObj->getDocumentMetaData();
        }           
    }
?>