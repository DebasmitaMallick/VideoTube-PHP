<?php
require_once("includes/header.php");
require_once("includes/classes/VideoPlayer.php");
require_once("includes/classes/VideoDetailsFormProvider.php");
require_once("includes/classes/VideoUploadData.php");
require_once("includes/classes/SelectThumbnail.php");
require_once("includes/classes/userPrivileges.php");

if(!User::isLoggedIn()) {
    header("Location: signIn.php");
}

if(!isset($_GET["videoId"])) {
    echo "No video selected";
    exit();
}

$video = new Video($con, $_GET["videoId"], $userLoggedInObj);
$hasDeleteAccess = userPrivileges::hasDeleteAccess($con, $userLoggedInObj->getId(), $video->getCategory());
if(($video->getUploadedBy() != $userLoggedInObj->getUsername() and $userLoggedInObj->getRole() == 'normalUser') and !$hasDeleteAccess) {
    echo "<p style='color:red'>You do not have access to delete this video</p>";
    exit();
}


$userId = $userLoggedInObj->getId();
$videoCategoryId = $video->getCategory();

$hasAccess = userPrivileges::hasDeleteAccess($con, $userId, $videoCategoryId);
if($userLoggedInObj->getRole() != 'normalUser'){
    $hasAccess = true;
}

if($hasAccess === false){
    echo "<p style='color:red'>You do not have access to delete</p>";
    exit();
}
?>
<?= 
'<script>
    Swal.fire({
    text: "Are you sure you want to delete the video?",
    icon: `warning`,
    showCancelButton: true,
    confirmButtonColor: `#d33`,
    cancelButtonColor: `#5cb85c`,
    confirmButtonText: `Yes`
    }).then((result) => {
    if (result.isConfirmed) {
        window.location.href = "deleteVideo.php?deleteVideoId='.$_GET["videoId"].'";
    }
    else{
        window.location.href = "watch.php?id='.$_GET["videoId"].'";
    }
    })
</script>'
?>