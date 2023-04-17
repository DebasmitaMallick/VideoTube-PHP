<?php
require_once("includes/classes/ButtonProvider.php"); 
class VideoInfoControls {

    private $video, $userLoggedInObj;

    public function __construct($video, $userLoggedInObj) {
        $this->video = $video;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create() {

        $likeButton = $this->createLikeButton();
        $dislikeButton = $this->createDislikeButton();
        $shareButton = $this->createShareButton();
        
        return "<div class='controls'>
                    $likeButton
                    $dislikeButton
                    $shareButton
                </div>";
    }

    private function createLikeButton() {
        $text = $this->video->getLikes();
        $videoId = $this->video->getId();
        $action = "likeVideo(this, $videoId)";
        $class = "likeButton";

        $imageSrc = "assets/images/icons/thumb-up.png";

        if($this->video->wasLikedBy()) {
            $imageSrc = "assets/images/icons/thumb-up-active.png";
        }

        return ButtonProvider::createButton($text, $imageSrc, $action, $class);
    }

    private function createDislikeButton() {
        $text = $this->video->getDislikes();
        $videoId = $this->video->getId();
        $action = "dislikeVideo(this, $videoId)";
        $class = "dislikeButton";

        $imageSrc = "assets/images/icons/thumb-down.png";

        if($this->video->wasDislikedBy()) {
            $imageSrc = "assets/images/icons/thumb-down-active.png";
        }

        return ButtonProvider::createButton($text, $imageSrc, $action, $class);
    }

    private function createShareButton() {

        return '<!-- Button trigger modal -->
        <button type="button" class="ml-2" data-toggle="modal" data-target="#shareModal">
            <i class="fas fa-share fa-lg" style="color: #8f8f8f;"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shareModalLabel">Share</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row" >
                            <div class="col-12 text-center" style="display: flex; align-items: center; justify-content: space-between;">
                                <i class="fab fa-facebook fa-4x" color="#3b5998"></i>
                                <i class="fab fa-whatsapp fa-4x" color="#25d366"></i>
                                <i class="fab fa-twitter fa-4x" color="#1da1f2"></i>
                                <i class="fas fa-envelope fa-4x" color="#888888"></i>
                                <i class="fab fa-linkedin fa-4x" color="#0077b5"></i>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div id="iscopied" style="text-align: right; width: 98%;"></div>
                            <div class="col-12 text-center"  style="display: flex; align-items: center; justify-content: space-between;">
                                
                                <input type="text" class="form-control" onload="getUrlFunction()" id="urlInput">

                                <!-- The button used to copy the text -->
                                <button class="btn ml-2" style="color: blue!important" onclick="copyTextFunction()">Copy text</button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        ';
        
        
    }
}
?>