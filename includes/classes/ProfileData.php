<?php
class ProfileData {
    
    private $con, $profileUserObj;

    public function __construct($con, $profileUsername) {
        $this->con = $con;
        $this->profileUserObj = new User($con, $profileUsername);
    }

    public function profileCard($row){
        return ('
        <div class="card mb-2" style="width:100%">
            <img class="card-img-top" src="'.$row["profilePic"].'" alt="Card image" style="width:57%; margin-left:auto; margin-right:auto;">
            <div class="card-body">
                <h4 class="card-title">'.$row["firstName"]." ".$row["lastName"].'</h4>
                <p class="card-text">Phone number: '.$row["number"].'</p>
                <button onclick="passValue('.$row['id'].')" type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Assign</button>
            </div>
        </div>'
    );
    }

    public function getProfileUserObj() {
        return $this->profileUserObj;
    }

    public function getProfileUsername() {
        return $this->profileUserObj->getUsername();
    }

    public function getProfileRole() {
        return $this->profileUserObj->getRole();
    }

    public function userExists() {
        $query = $this->con->prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(":username", $profileUsername);
        $profileUsername = $this->getProfileUsername();
        $query->execute();

        return $query->rowCount() != 0;
    }

    public function getCoverPhoto() {
        return "assets/images/coverPhotos/default-cover-photo.jpg";
    }

    public function getProfileUserFullName() {
        return $this->profileUserObj->getName();
    }

    public function getProfilePic() {
        return $this->profileUserObj->getProfilePic();
    }

    public function getSubscriberCount() {
        return $this->profileUserObj->getSubscriberCount();
    }

    public function getUsersVideos() {
        $query = $this->con->prepare("SELECT * FROM videos WHERE uploadedBy=:uploadedBy ORDER BY uploadDate DESC");
        $query->bindParam(":uploadedBy", $username);
        $username = $this->getProfileUsername();
        $query->execute();

        $videos = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $videos[] = new Video($this->con, $row, $this->profileUserObj->getUsername());
        }
        return $videos;
    }

    public function getAllUserDetails() {
        return array(
            "Name" => $this->getProfileUserFullName(),
            "Username" => $this->getProfileUsername(),
            "Role" => $this->getProfileRole(),
            "Subscribers" => $this->getSubscriberCount(),
            "Total views" => $this->getTotalViews(),
            "Sign up date" => $this->getSignUpDate()
        );
    }

    private function getTotalViews() {
        $query = $this->con->prepare("SELECT sum(views) FROM videos WHERE uploadedBy=:uploadedBy");
        $query->bindParam(":uploadedBy", $username);
        $username = $this->getProfileUsername();
        $query->execute();

        return $query->fetchColumn();
    }

    private function getSignUpDate() {
        $date = $this->profileUserObj->getSignUpDate();
        return date("F jS, Y", strtotime($date));
    }
}
?>
