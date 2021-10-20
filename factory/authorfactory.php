<?php
    class AuthorFactory extends PersonFactory {
        public function createNewUser(): Person
        {
            return new Author();
        }
    }
?>