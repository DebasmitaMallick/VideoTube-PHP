<?php
    require_once("includes/header.php");
    require_once("includes/classes/VideoPlayer.php");
    require_once("includes/classes/VideoDetailsFormProvider.php");
    require_once("includes/classes/VideoUploadData.php");
    require_once("includes/classes/SelectThumbnail.php");
    require_once("includes/classes/deleteVideoApproved.php");
    require_once("includes/classes/userPrivileges.php");

    if(!User::isLoggedIn()) {
        header("Location: signIn.php");
    }

    $video = new Video($con, $_GET["deleteVideoId"], $userLoggedInObj);
    $hasDeleteAccess = userPrivileges::hasDeleteAccess($con, $userLoggedInObj->getId(), $video->getCategory());
    if(($video->getUploadedBy() != $userLoggedInObj->getUsername() and $userLoggedInObj->getRole() == 'normalUser') and !$hasDeleteAccess) {
        echo "<p style='color:red'>You do not have access to delete this video</p>";
        exit();
    }

    if($userLoggedInObj->getRole() != "normalUser"){
        deleteVideoApproved::videoDeleteApproved($con, $_GET["deleteVideoId"]);
    }

    // delete action::STARTS
    $deleteVideoApproved = deleteVideoApproved::getDeleteStatus($con, $userLoggedInObj->getUsername(), $_GET["deleteVideoId"]);
    if($deleteVideoApproved or $userLoggedInObj->getRole() != 'normalUser'){
        // if($deleteVideoApproved == "approved" or $userLoggedInObj->getRole() != 'normalUser'){
        //     deleteVideoApproved::videoDeleteApproved($con, $_GET["deleteVideoId"]);
        // }
        if($deleteVideoApproved == "rejected"){
            deleteVideoApproved::videoDeleteDenied($con, $_GET["deleteVideoId"]);
        }
        if($deleteVideoApproved == "requested by other user"){
            $_SESSION['deleted'] = "<p style='color:red'>The video has already been requested by another user</p>";
            header("Location: processing.php");
            return;
        }
        if($deleteVideoApproved == "pending"){
            $_SESSION['deleted'] = "<p style='color:red'>Your request is on pending</p>";
            header("Location: processing.php");
            return;
        }
    }
    // delete action::ENDS
    else{
        $requested_by= $userLoggedInObj->getUsername();
        $video_id= $_GET["deleteVideoId"];
        if(isset($video_id) && isset($requested_by)) {
        
            $userLoggedInObj = new User($con, $_SESSION["userLoggedIn"]);
        
            $query = $con->prepare("INSERT INTO delete_request(videoId,requestedBy)
                                    VALUES(:video_id, :requested_by)");
            $query->bindParam(":video_id", $video_id);
            $query->bindParam(":requested_by", $requested_by);
        
            $query->execute();
            echo "delete request submitted";
        }
        else {
            echo "One or more parameters are not passed into subscribe.php the file";
        }
        return;
    }
?>
