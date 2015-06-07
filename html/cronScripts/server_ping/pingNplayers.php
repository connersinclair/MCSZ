<?php

const servers_top = 290;

if (isset($argv['1'])) {
	$st = $argv['1'];
} else {
	$st = "all";
}

$rankings = file_get_contents("/var/www/html/cronScripts/server_rankings/ranking.json");

$ranks = json_decode($rankings, true);
end($ranks);
$lastRank = key($ranks);
reset($ranks);

$con = mysqli_connect("localhost","root","UWWi6Vfb0W","mcsz");
if (!$con) die("No SQL connection");


if ($st == "usest") {
    $i = 1;
    $timestart = microtime(true);
    while($i <= servers_top && $i <= $lastRank) {
        $currId = $ranks[$i][0];
        $sql = "SELECT server_ip, server_port FROM `mcsz`.`mcsz_servers` WHERE server_id = '$currId'";
        $ipPort = mysqli_query($con, $sql);
        $ipPort = mysqli_fetch_assoc($ipPort);
        $ip = $ipPort['server_ip'];
        $port = $ipPort['server_port'];
        
        $JsonResult = shell_exec("php ".__DIR__."/server_ping_scripts/ping.php ".$ip." ".$port."");
        $JsonDecoded = json_decode($JsonResult, true);
		
        $fullArray[$currId] = $JsonDecoded;
        $i++;
    }
    
    $fullArray["timeGenerated"] = $timestart;
    $fullArray["timeTaken"] = round(microtime(true) - $timestart, 3);
    $usestEncoded = json_encode($fullArray);
    file_put_contents("/var/www/html/cronScripts/server_ping/rankingpings.json", $usestEncoded);
} else {
    $i = 1;
    $timestart = microtime(true);

    //Truncate the server_data table, which is the one being queried for results
    $sql = "TRUNCATE TABLE `mcsz`.`mcsz_server_data";
    mysqli_query($con, $sql);

    while($i <= $lastRank) {
        $currId = $ranks[$i][0];
        $sql = "SELECT server_ip, server_port FROM `mcsz`.`mcsz_servers` WHERE server_id = '$currId'";
        $ipPort = mysqli_query($con, $sql);
        $ipPort = mysqli_fetch_assoc($ipPort);
        $ip = $ipPort['server_ip'];
        $port = $ipPort['server_port'];
        
        $JsonResult = shell_exec("php ".__DIR__."/server_ping_scripts/ping.php ".$ip." ".$port."");
        $JsonDecoded = json_decode($JsonResult, true);
		
		$serverSuccess = $JsonDecoded['success'];
        $serverMax     = $JsonDecoded['max'];
        $serverCur     = $JsonDecoded['players'];
        $serverPing    = $JsonDecoded['ping'];

        //Enter data into the server_data table
		$sql = "INSERT INTO `mcsz`.`mcsz_server_data` (data_id, data_query_time, data_server_id, data_max_players, data_current_players, data_online, data_server_ping) VALUES ('', '$timestart', '$currId', '$serverMax', '$serverCur', '$serverSuccess', '$serverPing')";
        mysqli_query($con, $sql);

        //Enter data into the master server_data table, keeps all records
        $sql = "INSERT INTO `mcsz`.`mcsz_server_data_master` (data_id, data_query_time, data_server_id, data_max_players, data_current_players, data_online, data_server_ping) VALUES ('', '$timestart', '$currId', '$serverMax', '$serverCur', '$serverSuccess', '$serverPing')";
        mysqli_query($con, $sql);
        
        $fullArray[$currId] = $JsonDecoded;
        $i++;
    }
    
    $fullArray["timeGenerated"] = $timestart;
    $fullArray["timeTaken"] = round(microtime(true) - $timestart, 3);
    $usestEncoded = json_encode($fullArray);
    file_put_contents("/var/www/html/cronScripts/server_ping/rankingpings.json", $usestEncoded);
}

echo "\nTook ".$fullArray["timeTaken"]." to finish script\n$i servers pinged\n\n";
#print_r($fullArray);
#print_r(error_get_last());
?>