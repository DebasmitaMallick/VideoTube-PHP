<?php
    require_once("includes/header.php");
    require_once("includes/classes/ProfileGenerator.php");
    require_once("includes/classes/notificationsVideosProvider.php");
    require_once("includes/classes/userPrivileges.php");

    if(!User::isLoggedIn()){
        header("Location: signIn.php");
        return;
    }

    if($userLoggedInObj->getRole() != 'normalUser'){
        echo "<p style='color: red'>You cannot access this page</p>";
        exit();
    }

    // for privilege table---------------------------
    $userId = $userLoggedInObj->getId();
    $privilageData = userPrivileges::getPrivileges($con,$userId);

    //for request status-------------------------------
    $videoProvider = new notificationsVideosProvider($con, $userLoggedInObj);
    #Step1
    $videos = $videoProvider->getRequestedVideos();
    #Step2
    $videoGrid = new VideoGrid($con, $userLoggedInObj);

    $query1 = $con->query("SELECT * FROM delete_request");
    $query2 = $con->prepare("UPDATE `delete_request` SET `seen` = '1' WHERE id = :requestId");
    $query3 = $con->prepare("DELETE FROM `delete_request` WHERE `delete_request`.`id` = :delete_requestId; DELETE FROM thumbnails WHERE thumbnails.videoId = :video_id;");
    while ($row = $query1->fetch(PDO::FETCH_ASSOC)){
        if(isset($_POST[$row['id']])){
            $query2->bindParam(":requestId", $row['id']);
            $query2->execute();
            header("Location: normalUser.php");
            return;
        }
        if(isset($_POST['deleteThis'.$row['id']])){
            $query3->bindParam(":delete_requestId", $row['id']);
            $query3->bindParam(":video_id", $row['videoId']);
            $query3->execute();
            header("Location: normalUser.php");
            return;
        }
    }

?>
<div class="container d-flex flex-column">
    <div>
        <?php
            require("profile.php");
        ?>
    </div>

    <div class="mt-3 row normalUserInfo">
        <div id="privilegesCard" class="card col">
            <div class="card-header bg-info text-white text-center">
                <div class="card-title">Privileges</div>
                <div class="card-tools">
                    <button onclick="plusMinus(this)" type="button" class="btn btn-info btn-sm text-white" data-toggle="collapse" data-target="#privilegesBody">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div id="privilegesBody" class="collapse show">
                <div class="card-body">
                    <?= $privilageData ?>
                </div>
            </div>
        </div>

        <div id="requestStatusCard" class="card col">
            <div class="card-header bg-info text-white text-center">
                <div class="card-title">Request Status</div>
                <div class="card-tools">
                    <button onclick="plusMinus(this)" type="button" class="btn btn-info btn-sm text-white" data-toggle="collapse" data-target="#requestStatusBody">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div id="requestStatusBody" class="collapse show">
                <div class="card-body">
                    <div class="largeVideoGridContainer">
                        <?php
                        if(is_array($videos)){
                            echo $videoGrid->createLargeRequests($videos, null, true);
                        }
                        elseif($videos === false){
                            echo "No more requests";
                        }
                        else{
                            echo $videos;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
