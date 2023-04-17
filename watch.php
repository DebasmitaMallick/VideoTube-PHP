<?php 
require_once("includes/header.php"); 
require_once("includes/classes/VideoPlayer.php"); 
require_once("includes/classes/VideoInfoSection.php"); 
require_once("includes/classes/Comment.php"); 
require_once("includes/classes/CommentSection.php"); 

$create = true;
if(!isset($_SESSION["userLoggedIn"])){ 
        $create = false;
        $_SESSION['previous-page'] = "watch.php?id=".$_GET["id"];
        echo '<script>
        Swal.fire({
            title: `Please Sign In`,
            icon: `warning`,
            showCancelButton: true,
            confirmButtonColor: `#3085d6`,
            cancelButtonColor: `#d33`,
            confirmButtonText: `Sign In`
            }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "signIn.php";
            }
            else{
                window.location.href = "index.php";
            }
            })
        </script>';
        return;
}

if(!isset($_GET["id"])) {
    echo "No url passed into page";
    exit();
}


$video = new Video($con, $_GET["id"], $userLoggedInObj);
$video->incrementViews();
?>
<script src="assets/js/videoPlayerActions.js"></script>
<script src="assets/js/commentActions.js"></script>

<div class="watchLeftColumn">

<?php
    $videoPlayer = new VideoPlayer($video);
    echo $videoPlayer->create($create);

    $videoPlayer = new VideoInfoSection($con, $video, $userLoggedInObj);
    echo $videoPlayer->create();

    $commentSection = new CommentSection($con, $video, $userLoggedInObj);
    echo $commentSection->create();
?>


</div>

<div class="suggestions">
    <?php
    $videoGrid = new VideoGrid($con, $userLoggedInObj);
    echo $videoGrid->create(null, null, false);
    ?>
</div>




<?php require_once("includes/footer.php"); ?>
                