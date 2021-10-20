<?php
    abstract class PersonFactory {
        abstract public function createNewUser($username, $password, $email, $dob) : Person;
    }
?>