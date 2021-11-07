<?php
    abstract class PersonFactory {
        //only for getting from database
        abstract public function getNewUser($personID);

        public function operation($personID) {
            $person = $this->getNewUser($personID);

            $str = "The person type is " + $person->type;

            return $str;
        }
    }

    class EditorFactory extends PersonFactory {
        public function getNewUser($personID) : Editor
        {
            global $personTable;
            $query = "SELECT * FROM `$personTable` WHERE `personID` = ?";
            $result = sqlProcesses($query, "s", [$personID]);

            $personobj = "";
            if (mysqli_num_rows($result) == 1) {
                while ($user = mysqli_fetch_assoc($result)) {
                    //create person
                    if ($user['type'] == 0) {
                        $personobj = new Editor();
                        $personobj->updatePersonData($personID, $user['username'], $user['password'],
                                                $user['email'], $user['dob']);
                    }
                    else {
                        $personobj = ["error", "person types don't match"];
                    }
                }
            }
            
            return $personobj;
        }
    }

    class AuthorFactory extends PersonFactory {
        public function getNewUser($personID) : Author
        {
            global $personTable;
            $query = "SELECT * FROM `$personTable` WHERE `personID` = ?";
            $result = sqlProcesses($query, "s", [$personID]);

            $personobj = "";
            if (mysqli_num_rows($result) == 1) {
                while ($user = mysqli_fetch_assoc($result)) {
                    //create person
                    if ($user['type'] == 1) {
                        $personobj = new Author();
                        $personobj->updatePersonData($personID, $user['username'], $user['password'],
                                                $user['email'], $user['dob']);
                    }
                    else {
                        $personobj = ["error", "person types don't match"];
                    }
                }
            }
            
            return $personobj;
        }
    }

    class ReviewerFactory extends PersonFactory {
        public function getNewUser($personID) : Reviewer
        {
            global $personTable, $reviewerTable;
            $query = "SELECT * FROM `$personTable` WHERE `personID` = ?";
            $result = sqlProcesses($query, "s", [$personID]);

            $personobj = "";
            if (mysqli_num_rows($result) == 1) {
                while ($user = mysqli_fetch_assoc($result)) {
                    //create person
                    if ($user['type'] == 2) {
                        $personobj = new Reviewer();
                        $personobj->updatePersonData($personID, $user['username'], $user['password'],
                                                    $user['email'], $user['dob']);

                        $rquery = "SELECT * FROM `$reviewerTable` WHERE `personID` = ?";

                        $result = sqlProcesses($rquery, "s", [$personID]);
            
                        while ($row = mysqli_fetch_assoc($result)) {
                            $personobj->areaOfExpertise = $row['areaOfExpertise'];
                            $personobj->status = $row['status'];
                        }
                    }
                    else {
                        $personobj = ["error" => "person types don't match"];
                    }
                }
            }

            return $personobj;
        }
    }
?>