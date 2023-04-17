<?php
class NavigationMenuProvider {

    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create() {
        $iconArray = array("assets/images/icons/history.png", "assets/images/icons/trending.png", "assets/images/icons/subscriptions.png","assets/images/icons/thumb-up.png", "assets/images/icons/history.png", "assets/images/icons/trending.png", "assets/images/icons/subscriptions.png","assets/images/icons/thumb-up.png","assets/images/icons/history.png", "assets/images/icons/trending.png", "assets/images/icons/subscriptions.png","assets/images/icons/thumb-up.png","assets/images/icons/history.png", "assets/images/icons/trending.png", "assets/images/icons/subscriptions.png","assets/images/icons/thumb-up.png");
        $menuHtml = $this->createNavItem("Home", "assets/images/icons/home.png", "index.php");
        $stmt = $this->con->query("SELECT id,name FROM categories");
        $i = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $menuHtml .= $this->createNavItem($row["name"], $iconArray[$i], "categoryVideos.php?categoryId=".$row['id']);
            $i += 1;
        }

        if(User::isLoggedIn()) {
            $menuHtml .= $this->createSubscriptionsSection();
        }

        return "<div class='navigationItems'>
                    $menuHtml
                </div>";
    }

    private function createNavItem($text, $icon, $link) {
        return "<div class='navigationItem'>
                    <a href='$link'>
                        <img src='$icon'>
                        <span>$text</span>
                    </a>
                </div>";
    }

    private function createSubscriptionsSection() {
        $subscriptions = $this->userLoggedInObj->getSubscriptions();

        $html = "<span class='heading'>Subscriptions</span>";
        foreach($subscriptions as $sub) {
            $subUsername = $sub->getUsername();
            $html .= $this->createNavItem($subUsername, $sub->getProfilePic(), "profile.php?username=$subUsername");
        }
        return $html;
    }

}
?>