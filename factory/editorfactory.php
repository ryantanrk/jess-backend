<?php
    require_once 'class\person.php';
    class EditorFactory extends PersonFactory {
        public function createNewUser($personID, $username, $password, $email, $dob): Person
        {
            //create personID
            $personobj = new Editor($personID, $username, $password, $email, $dob);
            
            return $personobj;
        }
    }
?>