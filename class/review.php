<?php
class Review {
    public $reviewerID;
    public $documentID;
    public $rating;
    public $comment;
    public $status;
    public $completionDate;

    public function __construct($reviewerID, $documentID)
    {
        //create
        $this->reviewerID = $reviewerID;
        $this->documentID = $documentID;
        $this->rating = -1;
        $this->comment = "";
        $this->status = "pending";
        $this->completionDate = "";
    }

    public function setDocumentRating($rating, $comment)
    {
        $this->rating = $rating;
        $this->comment = $comment;
        $this->status = "complete";
        $this->completionDate = date('Y-m-d');
    }

    public function setReview($rating, $comment, $status, $completionDate)
    {
        $this->rating = $rating;
        $this->comment = $comment;
        $this->status = $status;
        $this->completionDate = $completionDate;
    }
}
?>