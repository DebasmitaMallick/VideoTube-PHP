<?php
class VideoGrid {

    private $con, $userLoggedInObj;
    private $largeMode = false;
    private $gridClass = "videoGrid";

    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create($videos, $title, $showFilter) {

        if($videos == null) {
            $gridItems = $this->generateItems();
        }
        else {
            $gridItems = $this->generateItemsFromVideos($videos);
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
    public function createNotificationList($videos, $title, $showFilter) {

        if($videos == null) {
            $gridItems = $this->generateItems();
        }
        else {
            $gridItems = $this->generateNotificationItemsFromVideos($videos);
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
    
    public function generateItems() {
        $query = $this->con->prepare("SELECT * FROM videos ORDER BY RAND() LIMIT 15");
        $query->execute();
        
        $elementsHtml = "";
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }

    public function generateItemsFromVideos($videos) {
        $elementsHtml = "";

        foreach($videos as $video) {
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }
    public function generateNotificationItemsFromVideos($videos) {
        $elementsHtml = 
        '<form method="post"><table class="table" id="notificationTable">
            <thead>
                <tr>
                    <th>Video</th>
                    <th>Requested By</th>
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
            $elementsHtml .= '<tr><td>'.$item->create().'</td><td>'.$row['requestedBy'].'</td>';
            $elementsHtml .= '<td><button style="font-size: 0.8rem;" type="submit" name="approve'.$row['id'].'" class="btn btn-primary mr-2 mt-2">Approve</button><button style="font-size: 0.8rem;" type="submit" name="reject'.$row['id'].'" class="btn mt-2">Reject</button></td></tr>';
        }
        $elementsHtml .= '</tbody></table></form>';
        return $elementsHtml;
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
            if(($row['status'] == '1') or ($row['status'] == '0')){
                $elementsHtml .= '<td><button type="submit" name="'.$row['id'].'" class="btn btn-light">OK</button></td></tr>';
            }
        }

        // get the deleted video thumbnails
        $allElementsHtml = $this->getDeletedVideoStatus($elementsHtml);
        $allElementsHtml .= '</tbody></table></form>';
        return $allElementsHtml;
    }

    public function getDeletedVideoStatus($elementsHtml){
        $user = $this->userLoggedInObj->getUsername();
        $deletedVideoQuery = $this->con->prepare("SELECT * FROM delete_request WHERE delete_request.status = '1' AND delete_request.seen = '0' AND delete_request.requestedBy = :user");
        $deletedVideoQuery->bindParam(":user",$user);
        $thumbnailQuery = $this->con->prepare("SELECT * FROM thumbnails WHERE thumbnails.videoId = :videoId and thumbnails.selected = '1'");
        $deletedVideoQuery->execute();
        while($row = $deletedVideoQuery->fetch(PDO::FETCH_ASSOC)){
            $thumbnailQuery->bindParam(":videoId",$row['videoId']);
            $thumbnailQuery->execute();
            $row2 = $thumbnailQuery->fetch(PDO::FETCH_ASSOC);
            $thumbnail = $row2['filePath'];
            $elementsHtml .= '<tr><td><a href="doesNotExist.php"><div class="videoGridItem"><div class="thumbnail"><img src='.$thumbnail.'></div><div class="details"><h3 class="title">'.$row2['title'].'</h3></div></div></a></td>';
            $elementsHtml .= '<td><h5><span class="badge badge-danger">Deleted</span></h5></td>';
            $elementsHtml .= '<td><button type="submit" name="deleteThis'.$row['id'].'" class="btn btn-light">OK</button></td></tr>'; 
        }
        return $elementsHtml;
    }
    
    public function createGridHeader($title, $showFilter) {
        $filter = "";

        if($showFilter) {
            $link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            
            $urlArray = parse_url($link);
            $query = $urlArray["query"];

            parse_str($query, $params);

            unset($params["orderBy"]);
            
            $newQuery = http_build_query($params);

            $newUrl = basename($_SERVER["PHP_SELF"]) . "?" . $newQuery;

            $filter = "<div class='right'>
                            <span>Order by:</span>
                            <a href='$newUrl&orderBy=uploadDate'>Upload date</a>
                            <a href='$newUrl&orderBy=views'>Most viewed</a>
                        </div>";
        }

        return "<div class='videoGridHeader'>
                        <div class='left'>
                            $title
                        </div>
                        $filter
                    </div>";
    }

    public function createLarge($videos, $title, $showFilter) {
        $this->gridClass .= " large";
        $this->largeMode = true;
        return $this->create($videos, $title, $showFilter);
    }
    public function createLargeNotifications($videos, $title, $showFilter) {
        $this->gridClass .= " large";
        $this->largeMode = true;
        return $this->createNotificationList($videos, $title, $showFilter);
    }
    public function createLargeRequests($videos, $title, $showFilter) {
        $this->gridClass .= " large";
        $this->largeMode = true;
        return $this->createRequestsList($videos, $title, $showFilter);
    }

}
?>