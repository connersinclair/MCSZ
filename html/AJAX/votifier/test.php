<?php
error_reporting(E_ALL);

// Details of the vote.
$str = "VOTE\n" .
      "mcserverstatus\n" .
      "ryanshawty\n" .
      "203.0.113.1\n" .
      time()."\n";

// Fill in empty space to make the encrypted block 256 bytes.
$leftover = (256 - strlen($str)) / 2;

while ($leftover > 0) {
    $str .= "\x0";
    $leftover--;
}

// The public key, this is an example.
$key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnlUNGrZ9uNx+qddRgSiJlCmrR0ewIU6fziXlq/TeK3yQ8rhVoqd7G1n1at1+VBTh1qKEHhdU08v4YXMdQ2YK6BCp6a+8s7zAoFS01Er6lq7HLdu+lk6cEwXGhCWE3FDAL2HWoDaKeULISDu8TjMC5f3ef7zteaF2BeJr550gkst17GilojR+dPg3W8J3H+dmLWtfTN7a4Y5oldfgjlRkW8erDmizcI2KmjMjeTSg8j2+pUdlsSEMIV9ZCZqaO015bZb7cA3OTJtxUqe1IxlkjSVSzZ4ZaiB7bniH3vftENefieAFU7Q9BGbJ068jgeSx73lyRl4dKlomr6gYAn7AdwIDAQAB";
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

if (!$socket) {
    die("Failed to connect to Votifier.");
}

// Send the contents of the encrypted block to Votifier.
fwrite($socket, $encrypted);