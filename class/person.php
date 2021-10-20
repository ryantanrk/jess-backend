<?php
    abstract class Person {
        public $personID;
        public $username;
        public $password;
        public $email;
        public $dob;

        abstract public function update();
        abstract public function getManuscript();
        abstract public function setManuscript();
    }
?>