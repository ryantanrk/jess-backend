<?php
    class EditorFactory extends PersonFactory {
        public function createNewUser($username, $password, $email, $dob): Person
        {
            $type = 0;

            //create personID
            $personobj = new Person();
            $personID = $personobj->getLastID;
            
            return new Editor($personID, $username, $password, $email, $dob);
        }
    }
?>