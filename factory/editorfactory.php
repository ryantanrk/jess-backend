<?php
    class EditorFactory extends PersonFactory {
        public function createNewUser(): Person
        {
            return new Editor();
        }
    }
?>