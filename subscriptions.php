<?php
require_once("includes/header.php");

if(!User::isLoggedIn()) {
    $_SESSION['previous-page'] = "subscriptions.php";
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
}

$subscriptionsProvider = new SubscriptionsProvider($con, $userLoggedInObj);
$videos = $subscriptionsProvider->getVideos();

$videoGrid = new VideoGrid($con, $userLoggedInObj);
?>
<div class="largeVideoGridContainer">
    <?php
    if(sizeof($videos) > 0) {
        echo $videoGrid->createLarge($videos, "New from your subscriptions", false);
    }
    else {
        echo "No videos to show";
    }
    ?>
</div>