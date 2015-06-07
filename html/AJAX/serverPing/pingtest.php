<?php
$serverIP = $_GET['server'];
$serverPort = $_GET['port'];
if (preg_match("/^[A-Za-z0-9\-\.]+$/", $serverIP)) {
    if (preg_match("/^[0-9]+$/", $serverPort)) {
        echo exec("python ping.py");
        print "Started";
    } else {
        echo "The port can only contain numbers";
    }
} else {
    echo "Please enter a valid IP address (not related to the ping test itself)";
}
