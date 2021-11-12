<?php
    require_once 'person.php';

    abstract class PersonFactory {
        abstract public function createNewUser($personID, $username, $password, $email, $dob) : Person;
    }

    //Create an Editor; Editor is the only class that uses singleton
    class EditorFactory extends PersonFactory 
    {
        public function createNewUser($personID, $username, $password, $email, $dob) : Person
        {
            $personobj = new Editor();
            $personobj->updatePersonData($personID, $username, $password, $email, $dob);

            return $personobj;
        }
    }

    //Create an Author
    class AuthorFactory extends PersonFactory 
    {
        public function createNewUser($personID, $username, $password, $email, $dob) : Person
        {
            $personobj = new Author();
            $personobj->updatePersonData($personID, $username, $password, $email, $dob);

            return $personobj;
        }
    }

    //Create a Reviewer
    class ReviewerFactory extends PersonFactory 
    {
        public function createNewUser($personID, $username, $password, $email, $dob) : Person
        {
            $personobj = new Reviewer();
            $personobj->updatePersonData($personID, $username, $password, $email, $dob);

            //get areaofexpertise and status from reviewerspecific table
            $result = sqlProcesses("SELECT `areaOfExpertise`, `status` FROM `reviewerSpecific`
                WHERE `personID` = ?", "s", [$personID]);
            
            $row = mysqli_fetch_assoc($result);
            
            //set additional reviewer attributes
            $personobj->areaOfExpertise = $row['areaOfExpertise'];
            $personobj->status = $row['status'];

            return $personobj;
        }
    }

    //A simpler way to create new Person objects
    function clientCode($PersonFactory, $personID, $username, $password, $email, $dob) : Person
    {
        // $newPerson = $PersonFactory->createNewUser($personID, $username, $password, $email, $dob);
        // return $newPerson;

        //Replace temp with query
        return $PersonFactory->createNewUser($personID, $username, $password, $email, $dob);
    }

    //function to get person object from personID
    function getPersonFromID($personID)
    {
        $result = sqlProcesses("SELECT `personID`, `type`, `username`, `email`, `dob` FROM `person`
            WHERE `personID` = ?", "s", [$personID]);

        if (mysqli_num_rows($result) == 1) {
            $personobj = "";
            $row = mysqli_fetch_assoc($result);
            switch ($row['type']) {
                case 0: //editor
                    $factoryobj = new EditorFactory;
                    $personobj = $factoryobj->createNewUser($row['personID'], $row['username'], "", $row['email'], $row['dob']);
                    break;
                case 1: //author
                    $factoryobj = new AuthorFactory;
                    $personobj = $factoryobj->createNewUser($row['personID'], $row['username'], "", $row['email'], $row['dob']);
                    break;
                case 2: //reviewer
                    $factoryobj = new ReviewerFactory;
                    $personobj = $factoryobj->createNewUser($row['personID'], $row['username'], "", $row['email'], $row['dob']);
                    break;
            }

            return $personobj;
        }
        else {
            print_r("Unable to get " . $personID);
            return false;
        }
    }
?>