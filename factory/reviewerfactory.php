<?php
    class ReviewerFactory extends PersonFactory {
        public function createNewUser($username, $password, $email, $dob): Person
        {
            $type = 2;

            //create personID
            $personobj = new Person();
            $personID = $personobj->getLastID;

            return new Reviewer($personID, $username, $password, $email, $dob);
        }
    }
?>