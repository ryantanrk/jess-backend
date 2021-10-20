<?php
    class ReviewerFactory extends PersonFactory {
        public function createNewUser(): Person
        {
            return new Reviewer();
        }
    }
?>