<?php
    class getRequestStatus{
        #SELECT * FROM thumbnails WHERE thumbnails.videoId = '215' and thumbnails.selected = '1'
        #SELECT * FROM delete_request WHERE delete_request.status = '1' AND delete_request.seen = '0' AND delete_request.requestedBy = 'userTwo'
    }
    #Step1
    $videos = $videoProvider->getRequestedVideos();

    public static function getRequestedVideos() {
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

        return $videos;
    }
    #Step2
    $videoGrid = new VideoGrid($con, $userLoggedInObj);
    echo $videoGrid->createLargeRequests($videos, null, true);

    public function createLargeRequests($videos, $title, $showFilter) {
        $this->gridClass .= " large";
        $this->largeMode = true;
        return $this->createRequestsList($videos, $title, $showFilter);
    }
    public function createRequestsList($videos, $title, $showFilter) {

        if($videos == null) {
            $gridItems = $this->generateItems();
        }
        else {
            $gridItems = $this->generateRequestItemsFromVideos($videos);
        }

        $header = "";

        if($title != null) {
            $header = $this->createGridHeader($title, $showFilter);
        }

        return "$header
                <div class='$this->gridClass'>
                    $gridItems
                </div>";
    }
    public function generateRequestItemsFromVideos($videos) {
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

        foreach($videos as $video) {
            $item = new VideoGridItem($video, $this->largeMode);
            $query = $this->con->prepare("SELECT * FROM delete_request WHERE videoId = :videoId");
            $videoId = $item->getVideoId();
            $query->bindParam(":videoId", $videoId);
            $query->execute();
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $elementsHtml .= '<tr><td>'.$item->create().'</td>';
            if($row['status'] == '1'){
                $elementsHtml .= '<td><h5><span class="badge badge-success">Approved</span></h5></td>';
            }elseif(($row['status'] == '0')){
                $elementsHtml .= '<td><h5><span class="badge badge-danger">Rejected</span></h5></td>';
            }
            else{
                $elementsHtml .= '<td><h5><span class="badge badge-warning">Pending</span></h5></td>';
            }
            $elementsHtml .= '<td><button type="submit" name="'.$row['id'].'" class="btn btn-light">OK</button></td></tr>';
        }

        // get the deleted video thumbnails
        $user = $this->userLoggedInObj->getUsername();
        $deletedVideoQuery = $this->con->prepare("SELECT * FROM delete_request WHERE delete_request.status = '1' AND delete_request.seen = '0' AND delete_request.requestedBy = $user");
        $thumbnailQuery = $this->con->prepare("SELECT * FROM thumbnails WHERE thumbnails.videoId = :videoId and thumbnails.selected = '1'");
        $deletedVideoQuery->execute();
        while($row = $deletedVideoQuery->fetch(PDO::FETCH_ASSOC)){
            $thumbnailQuery->bindParam(":videoId",$row['videoId']);
            $thumbnailQuery->execute();
            $row2 = $thumbnailQuery->fetch(PDO::FETCH_ASSOC);
            $thumbnail = $row2['filePath'];
            $elementsHtml .= '<tr><td><a href="doesNotExist.php"><div class="videoGridItem"><div class="thumbnail"><img src='.$thumbnail.'></div></div></a></td>';
            $elementsHtml .= '<td><h5><span class="badge badge-danger">Deleted</span></h5></td>';
            $elementsHtml .= '<td><button type="submit" name="'.$row['id'].'" class="btn btn-light">OK</button></td></tr>'; 
        }
        
        $elementsHtml .= '</tbody></table></form>';
        return $elementsHtml;
    }
    public function create() {
        $thumbnail = $this->createThumbnail();
        // $details = $this->createDetails();
        // $url = "watch.php?id=" . $this->video->getId();

        return "<a href='doesNotExist.php'><div class='videoGridItem'>$thumbnail</div></a>";
    }
    private function createThumbnail() {
        
        $thumbnail = $this->video->getThumbnail();
        // $duration = $this->video->getDuration();

        return "<div class='thumbnail'><img src='$thumbnail'></div>";

    }

    // private function createDetails() {
    //     $title = $this->video->getTitle();
    //     $username = $this->video->getUploadedBy();
    //     $views = $this->video->getViews();
    //     $description = $this->createDescription();
    //     $timestamp = $this->video->getTimeStamp();

    //     return "<div class='details'>
    //                 <h3 class='title'>$title</h3>
    //                 <span class='username'>$username</span>
    //                 <div class='stats'>
    //                     <span class='viewCount'>$views views - </span>
    //                     <span class='timeStamp'>$timestamp</span>
    //                 </div>
    //                 $description
    //             </div>";
    // }
?>