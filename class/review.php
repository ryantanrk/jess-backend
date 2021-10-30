<?php
class Review {
    public $reviewerID;
    public $documentID;
    public $rating;
    public $comment;

    public function __construct($reviewerID, $documentID, $rating, $comment)
    {
        $this->reviewerID = $reviewerID;
        $this->documentID = $documentID;
        $this->rating = $rating;
        $this->comment = $comment;
    }
}
?>