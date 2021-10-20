<?php
    class AuthorFactory extends PersonFactory {
        public function createNewUser($username, $password, $email, $dob): Person
        {
            $type = 1;

            //create personID
            $personobj = new Person();
            $personID = $personobj->getLastID;

            return new Author($personID, $username, $password, $email, $dob);
        }
    }
?>