<?php

    class deleteVideoApproved {

        public static function getDeleteStatus($con, $username, $videoId){
            $query = $con->prepare("SELECT * FROM delete_request WHERE delete_request.videoId = $videoId");
            // $query->bindParam(":username", $username);
            $query->execute();
            $status = 'null';
            $requestedBy = $username;
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $status = $row['status'];
                $requestedBy = $row['requestedBy'];
            }
            if($requestedBy != $username and $status != '1' and $status !== '0' and $status !== false){
                return "requested by other user";
            }
            elseif($status == '1'){
                return "approved";
            }
            elseif($status == '0'){
                return "rejected";
            }
            elseif($status == 'null'){
                return false;
            }
            else{
                return "pending";
            }
        }
        public static function videoDeleteApproved($con, $video_id){
            $stmt = $con->prepare("DELETE FROM `videos` WHERE `videos`.`id` = :video_id");
            $stmt->bindParam(':video_id', $video_id, PDO::PARAM_INT);
            $stmt2 = $con->prepare("DELETE FROM `likes` WHERE `likes`.`videoId` = :video_id");
            $stmt2->bindParam(':video_id', $video_id, PDO::PARAM_INT);
            $stmt3 = $con->prepare("DELETE FROM `dislikes` WHERE `dislikes`.`videoId` = :video_id");
            $stmt3->bindParam(':video_id', $video_id, PDO::PARAM_INT);
            $stmt4 = $con->prepare("DELETE FROM `comments` WHERE `comments`.`videoId` = :video_id");
            $stmt4->bindParam(':video_id', $video_id, PDO::PARAM_INT);
            // $stmt5 = $con->prepare("DELETE FROM `delete_request` WHERE `delete_request`.`videoId` = :video_id");
            // $stmt5->bindParam(':video_id', $video_id, PDO::PARAM_INT);
            if($stmt->execute() && $stmt2->execute() && $stmt3->execute() && $stmt4->execute()){
                $_SESSION['deleted'] = "<p style='color:green'>Video Deleted Successfully</p>";
            }
            header("Location: processing.php");
            return;
        }

        public static function videoDeleteDenied($con, $video_id){
            $stmt = $con->prepare("DELETE FROM `delete_request` WHERE `delete_request`.`videoId` = :video_id");
            $stmt->bindParam(':video_id', $video_id, PDO::PARAM_INT);
            if($stmt->execute()){
                $_SESSION['deleted'] = "<p style='color:red'>You cannot delete this video</p>";
            }
            header("Location: processing.php");
            return;
        }
        }

?>