<?php
    require_once 'class\person.php';
    class ReviewerFactory extends PersonFactory {
        public function createNewUser($personID, $username, $password, $email, $dob): Person
        {
            //create personID
            $personobj = new Reviewer($personID, $username, $password, $email, $dob);

            return $personobj;
        }
    }
?>