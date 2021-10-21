<?php
    require_once 'class\person.php';
    class AuthorFactory extends PersonFactory {
        public function createNewUser($personID, $username, $password, $email, $dob): Person
        {
            //create personID
            $personobj = new Author($personID, $username, $password, $email, $dob);

            return $personobj;
        }
    }
?>