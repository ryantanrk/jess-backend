<?php
    require_once 'connection.php';
    require_once 'class/review.php';
    session_start();
    $reviewobj = new Review("D1", "R1");
    $reviewobj->setReview("10", "ok", "complete", "2000-01-01");
    $_SESSION['review'] = $reviewobj;

    header("Location: api/read/getreview.php");
?>