<?php
class Review {
    public $reviewerID;
    public $documentID;
    public $rating;
    public $comment;
    public $status;
    public $dueDate;

    public function __construct($reviewerID, $documentID)
    {
        //create
        $this->reviewerID = $reviewerID;
        $this->documentID = $documentID;
        $this->rating = -1;
        $this->comment = "";
    }

    public function setReview($rating, $comment, $status, $dueDate)
    {
        $this->rating = $rating;
        $this->comment = $comment;
        $this->status = $status;
        $this->dueDate = $dueDate;
    }
}
?>