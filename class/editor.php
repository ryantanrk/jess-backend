<?php
    class Editor extends Person {
        public $type = 0;

        function __construct($personID, $username, $password, $email, $dob) {
            //constructor
            $this->$personID = $personID;
            $this->$username = $username;
            $this->$password = $password;
            $this->$email = $email;
            $this->$dob = $dob;
        }

        public function getManuscript() {

        }

        public function setManuscript() {

        }

        public function updatePersonData() {
            
        }
    }
?>