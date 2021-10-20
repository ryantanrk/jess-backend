<?php
    require_once '../connection.php';
    abstract class Person {
        public $personID;
        public $type;
        public $username;
        public $password;
        public $email;
        public $dob;

        abstract public function getManuscript();
        abstract public function setManuscript();
        abstract public function updatePersonData();

        public function getLastID() {
            $query = "SELECT * FROM `$personTable`";

            //result parse to json
            $result = mysqli_query($connection, $query) or die(mysqli_error($connection));

            $returnID = 0;

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $personID = $row['personID'];
                $number = ltrim($personID, $personID[0]);
                if ($number > $returnID) {
                    $returnID = "P" + ($number + 1);
                }
            }

            return $returnID;
        }
    }
?>