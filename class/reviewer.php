<?php
    require_once 'person.php';
    require_once 'document.php';

    class Reviewer extends Person {
        public $type = 2;
        public $areaOfExpertise;
        public $status;

        public function getManuscript(AbstractDocument $documentObj) 
        {
            $documentObj->getDocumentMetaData();
            $documentObj->getDocumentContent();
            $documentObj->getDocumentReviews();
        }

        public function setManuscript(AbstractDocument $documentObj, $drArray) {

        }

        //--------------------------------------------------------------------- Observer method
        public function notify(AbstractDocument $documentObj) 
        {
            echo "Reviewer notified<br>";
        }

        public function rate($documentID, $rating, $comment) {
            global $arr, $reviewTable, $connection;
            $sql = "UPDATE `$reviewTable` SET rating = '$rating', comment = '$comment', status = 'complete'
                    WHERE documentID = '$documentID' AND reviewerID = '$this->personID' AND status = 'pending'";
    
            $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    
            if (mysqli_affected_rows($connection) > 0) {
                //if there are rows
                $arr = [
                    "documentID" => $documentID,
                    "reviewerID" => $this->personID,
                    "message" => "rating successful"
                ];
            }
            else {
                $arr = ["message" => "rating failed"];
            }
            return $arr;
        }

    }
?>