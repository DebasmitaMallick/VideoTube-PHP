<?php 
require_once("includes/config.php"); 
require_once("includes/classes/ButtonProvider.php"); 
require_once("includes/classes/User.php"); 
require_once("includes/classes/Video.php"); 
require_once("includes/classes/VideoGrid.php"); 
require_once("includes/classes/VideoGridItem.php");
require_once("includes/classes/SubscriptionsProvider.php"); 
require_once("includes/classes/NavigationMenuProvider.php"); 

$usernameLoggedIn = User::isLoggedIn() ? $_SESSION["userLoggedIn"] : "";
$userLoggedInObj = new User($con, $usernameLoggedIn);

$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
if (!strpos($url,'assignRoles.php')) {
    $searchLink = "search.php";
} else {
    $searchLink = " ";
}
if($userLoggedInObj->getRole() != 'normalUser'){
    $dashboardLink = 'admin.php';
}
else{
    $dashboardLink = 'normalUser.php';
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sanjivani</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" data-auto-replace-svg="nest"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> 
    <script src="assets/js/commonActions.js"></script>
    <script src="assets/js/userActions.js"></script>
    <script src="assets/js/copyText.js"></script>

</head>
<body>
    
    <div id="pageContainer">

        <div id="mastHeadContainer">
            
            <i class="fas fa-bars navShowHide"></i>
            <a class="logoContainer" href="index.php">
            <img src="assets\images\icons\sanjivaniLogo.png" title="logo" alt="Site logo">
            </a>
            <div id="backIcon" class="navitem-display-none"><img src="https://img.icons8.com/ios/24/000000/left.png"/></div>
            <div class="searchBarContainer">
                <form action="<?= $searchLink ?>" method="GET">
                    <input type="text" class="searchBar" name="term" placeholder="Search...">
                    <button class="searchButton">
                        <img src="assets/images/icons/search.png">
                    </button>
                </form>
            </div>
            <div id="searchIcon" class="navitem-display-none"><img src="assets/images/icons/search.png"></div>
            <div class="rightIcons">
                <a href="upload.php">
                    <img class="upload" src="assets/images/icons/upload.png">
                </a>
                <?= ButtonProvider::createUserProfileNavigationButton($con, $userLoggedInObj->getUsername()); ?>
                <!-- test:start -->
                <ul id="profileDropdown" class="list-group">
                    <a href="<?= $dashboardLink ?>" class="list-group-item list-group-item-action"><i id="dashboardIcon" class="fas fa-user-circle"></i> Dashboard</a>
                    <!-- <a href="<?= "profile.php?username=$usernameLoggedIn" ?>" class="list-group-item list-group-item-action"><i id="dashboardIcon" class="fas fa-user-circle"></i> Profile</a> -->
                    <a href="settings.php" class="list-group-item list-group-item-action"><img src="assets/images/icons/settings.png" alt=""> Setting</a>
                    <a href="confirmLogout.php" class="list-group-item list-group-item-action"><img src="assets/images/icons/logout.png" alt=""> Logout</a>
                    
                </ul>
                <!-- test:end -->
            </div>

        </div>

        <div id="sideNavContainer" style="width: 208px;">
            <div id="nav-ham" style="display: none">
                <i class="fas fa-bars navShowHide"></i>
                <a id="logo-top" class="logoContainer" href="index.php">
                    <img src="assets\images\icons\sanjivaniLogo.png" title="logo" alt="Site logo" style="width: 4rem">
                </a>
            </div>
            <?php
            $navigationProvider = new NavigationMenuProvider($con, $userLoggedInObj);
            echo $navigationProvider->create();
            ?>
        </div>

        <div id="mainSectionContainer" style="padding-left: 208px">
            <div id="mainContentContainer">
