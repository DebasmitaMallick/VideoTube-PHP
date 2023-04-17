<?php
require_once("includes/header.php");
require_once("includes/classes/categoryVideosProvider.php");

$videoProvider = new categoryVideosProvider($con, $userLoggedInObj);
$videos = $videoProvider->getVideos();

$videoGrid = new VideoGrid($con, $userLoggedInObj);
?>
<div class="largeVideoGridContainer">
    <?php
    if(sizeof($videos) > 0) {
        echo $videoGrid->createLarge($videos, "Videos of this category", false);
    }
    else {
        echo "No videos to show";
    }
    ?>
</div>