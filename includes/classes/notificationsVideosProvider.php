<?php
class notificationsVideosProvider {

    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos() {
        $videos = array();
        $query = $this->con->prepare("
        SELECT videos.id,uploadedBy,title,description,privacy,filePath,category,uploadDate,views,duration 
        FROM videos INNER JOIN delete_request 
        WHERE (videos.id = delete_request.videoId AND delete_request.status IS NULL)
        ");
        $query->execute();
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($this->con, $row, $this->userLoggedInObj);
            array_push($videos, $video);
        }

        return $videos;
    }
    public function getRequestedVideos() {
        $videos = array();
        $username = $_SESSION['userLoggedIn'];
        $query = $this->con->query("
        SELECT videos.id,uploadedBy,title,description,privacy,filePath,category,uploadDate,views,duration 
        FROM videos INNER JOIN delete_request 
        WHERE (videos.id = delete_request.videoId AND delete_request.requestedBy = '".$username."' AND delete_request.seen = '0')
        ");
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($this->con, $row, $this->userLoggedInObj);
            array_push($videos, $video);
        }

        if(!sizeof($videos) > 0) {
            $user = $this->userLoggedInObj->getUsername();
            $deletedVideoQuery = $this->con->prepare("SELECT * FROM delete_request WHERE delete_request.status = '1' AND delete_request.seen = '0' AND delete_request.requestedBy = :user");
            $deletedVideoQuery->bindParam(":user",$user);
            $deletedVideoQuery->execute();
            $row = $deletedVideoQuery->fetch(PDO::FETCH_ASSOC);
            if($row){
                $videoGrid = new videoGrid($this->con, $this->userLoggedInObj);
                $elementsHtml = 
                '<form method="post"><table class="table" id="requestStatusTable">
                    <thead>
                        <tr>
                            <th>Video</th>
                            <th>Status</th>
                            <th> </th>
                        </tr>
                    </thead>
                    <tbody>';
                    $allElementsHtml = $videoGrid->getDeletedVideoStatus($elementsHtml);
                    $allElementsHtml .= '</tbody></table></form>';
                    return $allElementsHtml;
            }
            else{
                return false;
            }
        }
        return $videos;
    }
}
?>