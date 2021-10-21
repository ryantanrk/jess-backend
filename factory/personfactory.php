<?php
    require_once 'class\person.php';
    
    require_once 'editorfactory.php';
    require_once 'authorfactory.php';
    require_once 'reviewerfactory.php';

    abstract class PersonFactory {
        abstract public function createNewUser($personID, $username, $password, $email, $dob) : Person;
    }
?>