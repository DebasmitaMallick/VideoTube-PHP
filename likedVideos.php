<?php
require_once("includes/header.php");
require_once("includes/classes/LikedVideosProvider.php");

if(!User::isLoggedIn()) {
    $_SESSION['previous-page'] = "likedVideos.php";
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

$likedVideosProvider = new LikedVideosProvider($con, $userLoggedInObj);
$videos = $likedVideosProvider->getVideos();

$videoGrid = new VideoGrid($con, $userLoggedInObj);
?>
<div class="largeVideoGridContainer">
    <?php
    if(sizeof($videos) > 0) {
        echo $videoGrid->createLarge($videos, "Videos that you have liked", false);
    }
    else {
        echo "No videos to show";
    }
    ?>
</div>