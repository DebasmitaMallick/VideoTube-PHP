<?php
require_once("includes/header.php");
require_once("includes/classes/ProfileGenerator.php");

if(isset($usernameLoggedIn)) {
    $profileUsername = $usernameLoggedIn;
}
else {
    echo "Channel not found";
    exit();
}
$profileGenerator = new ProfileGenerator($con, $userLoggedInObj, $profileUsername);
echo $profileGenerator->create();
?>