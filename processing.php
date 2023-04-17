<?php 
require_once("includes/header.php");
require_once("includes/classes/VideoUploadData.php");
require_once("includes/classes/VideoProcessor.php");
require_once("includes/classes/userPrivileges.php");
require_once("includes/classes/Video.php");
if(isset($_SESSION['deleted'])){
    echo $_SESSION['deleted'];
    unset($_SESSION['deleted']);
    return;
}

if(!isset($_POST["uploadButton"])) {
    echo "No file sent to page.";
    exit();
}
$userId = $userLoggedInObj->getId();
$hasAccess = userPrivileges::hasUploadAccess($con, $userId, $_POST["categoryInput"]);

if($userLoggedInObj->getRole() != 'normalUser'){
    $hasAccess = true;
}
if($hasAccess === false){
    echo "<p style='color:red'>You do not have access to upload</p>";
    exit();
}

// 1) create file upload data
$videoUpoadData = new VideoUploadData(
                            $_FILES["fileInput"], 
                            $_POST["titleInput"],
                            $_POST["descriptionInput"],
                            $_POST["privacyInput"],
                            $_POST["categoryInput"],
                            $userLoggedInObj->getUsername()   
                        );
$videoTitleExists = Video::videoTitleExists($con, $_POST["titleInput"]);
if($videoTitleExists){
    echo "<p style='color:red'>A video with this title already exists, please eneter a new title</p>";
    exit();
}

// 2) Process video data (upload)
$videoProcessor = new VideoProcessor($con);
$wasSuccessful = $videoProcessor->upload($videoUpoadData);

// 3) Check if upload was successful
if($wasSuccessful) {
    echo "<p style='color:green'>Upload successful</p>";
}
?>