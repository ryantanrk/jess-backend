<?php
    class Author extends Person {
        public $type = 1;

        function __construct($personID, $username, $password, $email, $dob) {
            //constructor
            $this->$personID = $this->getLastID();
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

        public function makePayment() {

        }
    }
?>