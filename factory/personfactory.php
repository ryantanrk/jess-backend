<?php
    //require_once '../class/person.php';

    abstract class PersonFactory {
        abstract public function createNewUser($personID, $username, $password, $email, $dob) : Person;
    }

    class EditorFactory extends PersonFactory {
        public function createNewUser($personID, $username, $password, $email, $dob) : Person
        {
            //create personID
            $personobj = new Editor($personID, $username, $password, $email, $dob);
            
            return $personobj;
        }
    }

    class AuthorFactory extends PersonFactory {
        public function createNewUser($personID, $username, $password, $email, $dob) : Person
        {
            //create personID
            $personobj = new Author($personID, $username, $password, $email, $dob);

            return $personobj;
        }
    }

    class ReviewerFactory extends PersonFactory {
        public function createNewUser($personID, $username, $password, $email, $dob) : Person
        {
            //create personID
            $personobj = new Reviewer($personID, $username, $password, $email, $dob);

            return $personobj;
        }
    }
?>