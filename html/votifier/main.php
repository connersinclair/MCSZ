<?php

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
$socket = fsockopen("184.18.202.133", "8192", $errno, $errstr, 2);

// Send the contents of the encrypted block to Votifier.
if ($socket)
{
	$_GET['GLOBALVAL'] = fread($socket, 14);
	fwrite($socket, $encrypted); 	//on success send encrypted packet to server
	$_GET['keyEnd'] = $str;
	return true;

}
else
	return false; //on fail return false
}