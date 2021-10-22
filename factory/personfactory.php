<?php
    //require_once '../class/person.php';

    abstract class PersonFactory {
        abstract public function createNewUser($personID, $username, $password, $email, $dob) : Person;

        public function operation($personID, $username, $password, $email, $dob) {
            $person = $this->createNewUser($personID, $username, $password, $email, $dob);

            $str = "The person type is " + $person->type;

            return $str;
        }
    }

    class EditorFactory extends PersonFactory {
        public function createNewUser($personID, $username, $password, $email, $dob) : Person
        {
            //create personID
            $personobj = new Editor($personID, $username, $password, $email, $dob);
            //echo "Editor created";
            return $personobj;
        }
    }

    class AuthorFactory extends PersonFactory {
        public function createNewUser($personID, $username, $password, $email, $dob) : Person
        {
            //create personID
            $personobj = new Author($personID, $username, $password, $email, $dob);
            //echo "Author created";

            return $personobj;
        }
    }

    class ReviewerFactory extends PersonFactory {
        public function createNewUser($personID, $username, $password, $email, $dob) : Person
        {
            //create personID
            $personobj = new Reviewer($personID, $username, $password, $email, $dob);
            //echo "Reviewer created";

            return $personobj;
        }
    }
?>