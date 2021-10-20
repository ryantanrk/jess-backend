<?php
    class AuthorFactory extends PersonFactory {
        public function createNewUser($username, $password, $email, $dob): Person
        {
            $type = 1;

            //create personID
            
            
            return new Author();
        }
    }
?>