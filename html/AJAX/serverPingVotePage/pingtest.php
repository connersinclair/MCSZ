<?php
	$serverIP = $_GET['server'];
	$serverPort = $_GET['port'];
	if (preg_match("/^[A-Za-z0-9\-\.]+$/", $serverIP)) {
		if (preg_match("/^[0-9]+$/", $serverPort)) {
			include_once 'MinecraftServerStatus/status.class.php';
			$status = new MinecraftServerStatus();
			
			//If server port not specified, leave blank
			$response = $status->getStatus($serverIP, $serverPort);
			if(!$response) {
				$serverResult = array("success" => false, "max" => 0, "players" => 0);
				echo json_encode($serverResult);
			} else {
				$serverResult = array("success" => true, "max" => $response['maxplayers'], "players" => $response['players']);
				echo json_encode($serverResult);
			}
		}
	}
?>
