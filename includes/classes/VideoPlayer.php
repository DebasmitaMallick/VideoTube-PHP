<?php
class VideoPlayer {

    private $video;

    public function __construct($video) {
        $this->video = $video;
    }

    public function create($autoPlay) {
        if($autoPlay) {
            $autoPlay = "autoplay";
        }
        else {
            $autoPlay = "";
        }
        $filePath = $this->video->getFilePath();
        return "<video id='videoElementID' class='videoPlayer' controls $autoPlay controlsList='nodownload'>
                    <source src='$filePath' type='video/mp4'>
                    Your browser does not support the video tag
                </video>";
    }

}
?>