<?php
$con = mysqli_connect("localhost","root","UWWi6Vfb0W","mcsz");
if ($con == true) {
	$timestart = microtime(true);
    $sql = "SELECT vote_server, COUNT(*) FROM `mcsz`.`mcsz_votes` GROUP BY vote_server ORDER BY COUNT(*) DESC";
    $rankings = mysqli_query($con, $sql);
    $i = 1;
    while ($server = mysqli_fetch_assoc($rankings)) {
        $rankArray[$i] = array($server['vote_server'], $server['COUNT(*)']);
        $i++;
    }
	$timeArray["timeGenerated"] = $timestart;
    file_put_contents("/var/www/html/cronScripts/server_rankings/ranking.json", json_encode($rankArray));
	file_put_contents("/var/www/html/cronScripts/server_rankings/timings.json", json_encode($timeArray));
}