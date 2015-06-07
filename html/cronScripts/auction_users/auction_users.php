<?php
const auctionRanksAllowed = 10;
$i;

error_reporting(~E_NOTICE & ~E_WARNING);

$con = mysqli_connect("localhost","root","UWWi6Vfb0W","mcsz");
if (!$con) die("No MySQL Database connection");

$rankedServers = json_decode(file_get_contents("/var/www/html/cronScripts/server_rankings/ranking.json"), true);
##Ranked server JSON file:
##$rankedServer['X']['0'] == Server ID


while ($i <= auctionRanksAllowed) {
    $serverID = $rankedServers[$i]['0'];
    $getOwner = "SELECT server_owner,server_name FROM `mcsz`.`mcsz_servers` WHERE server_id = '$serverID'";
    $owner = mysqli_query($con, $getOwner);
    $owner = mysqli_fetch_assoc($owner);

    $currentOwner = $owner['server_owner'];

    if (array_key_exists($currentOwner, $finalArray)) {
        if (strlen($finalArray[$currentOwner][1]) > 5) {
            $finalArray[$currentOwner][2] = $owner['server_name'];
        } else {
            if (strlen($finalArray[$currentOwner][0]) > 5) {
                $finalArray[$currentOwner][1] = $owner['server_name'];
            }
        }
    } else {
        $finalArray[$currentOwner][0] = $owner['server_name'];
    }

    #$finalArray[$currentOwner][$i] = $owner['server_name'];

    $i++;
}
array_shift($finalArray);
file_put_contents("/var/www/html/cronScripts/auction_users/allowed.json", json_encode($finalArray));
