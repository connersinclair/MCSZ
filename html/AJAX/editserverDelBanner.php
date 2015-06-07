<?php
require $_SERVER["DOCUMENT_ROOT"].'/required/dbcon.php';
$serverid = $_GET['serverid'];
$sql = "SELECT server_owner FROM `mcsz`.`mcsz_servers` WHERE server_id ='$serverid'";
$owner = mysqli_query($con, $sql);
$owner = mysqli_fetch_assoc($owner);
$owner = $owner['server_owner'];
$user = $_COOKIE['username'];
if ($owner != $user) {
    header("Location: /myservers");
} else {
    $sql = "SELECT server_bannerLocation FROM `mcsz`.`mcsz_servers` WHERE server_id ='$serverid'";
    $bannerLoc = mysqli_query($con, $sql);
    $bannerLoc = mysqli_fetch_assoc($bannerLoc);
    $bannerLoc = $_SERVER["DOCUMENT_ROOT"]."/banners/".$bannerLoc['server_bannerLocation'];
    unlink($bannerLoc);
    $sql = "UPDATE `mcsz`.`mcsz_servers` SET server_bannerLocation = '' WHERE server_id ='$serverid'";
    mysqli_query($con, $sql);
    echo "/static/nobanner.png";
}