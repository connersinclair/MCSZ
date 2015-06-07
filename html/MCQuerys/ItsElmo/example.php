<?php

	include_once 'MinecraftServerStatus/status.class.php';
	$status = new MinecraftServerStatus();
	
	//If server port not specified, leave blank
	$response = $status->getStatus('play.mcszdomaintest.tk', '27995');
	if(!$response) {
		echo"The Server is offline!";
	} else {
		echo"The Server ".$response['hostname']." is running on ".$response['version']." and is online,
		currently are ".$response['players']."/".$response['maxplayers']." players online.<br><br>
		The motd of the server is '".$response['motd']."'.<br><br> 
		The server has a ping of ".$response['ping']." milliseconds.";
	}

?>
