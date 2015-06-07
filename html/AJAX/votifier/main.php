<?php
//turn all errors on for debug
//ini_set('error_reporting', E_ALL);

//So we don't get a ton of errors from people not knowing what port to use
error_reporting(0);

//main function
function Votifier($public_key, $server_ip, $server_port, $username) 
{

//error_reporting(E_ALL);

// Details of the vote.
$str = "VOTE\n" .
      "MCSZ\n" .
      "MCSZ_TEST_PACKET\n" .
      "MCSZ.NET\n" .
      time()."\n";

// Fill in empty space to make the encrypted block 256 bytes.
$leftover = (256 - strlen($str)) / 2;

while ($leftover > 0) {
    $str .= "\x0";
    $leftover--;
}

// The public key, this is an example.
$key = $public_key;
$key = wordwrap($key, 65, "\n", true);
$key = <<<EOF
-----BEGIN PUBLIC KEY-----
$key
-----END PUBLIC KEY-----
EOF;

// Encrypt the string.
openssl_public_encrypt($str, $encrypted, $key);

// Establish a connection to Votifier.
$socket = fsockopen($server_ip, $server_port, $errno, $errstr, 2);

// Send the contents of the encrypted block to Votifier.
if ($socket)
{
	$_GET['GLOBALVAL'] = fread($socket, 14);
	fwrite($socket, $encrypted); 	//on success send encrypted packet to server
	return true;

}
else
	return false; //on fail return false
}

#-----------------------------------------------------#

$public_key = str_replace("%2B", "+", $_GET['key']); //you need to change this value 
$server_ip = $_GET['ip'];//you need to change this value
$server_port = $_GET['port']; //default is 8192
$username  = ""; //can be any string like 'test' for example

//call function, you can also improve the error reporting, this is only basic
if(Votifier($public_key, $server_ip, $server_port, $username)) {
	echo "<span class=\"text-success\">Our server successfully sent a packet to Votifier on your server!</span><br><br>Your server has reported the following information: <span title=\"This is the version of Votifier installed on your server\">".$_GET['GLOBALVAL']."</span>";
} else {
	echo "<span class=\"text-danger\">Something happened between our server and your Votifier listener. This most likely means you entered the wrong port for us to send to Votifier, or Votifier is incorrectly configured.</span>";
}