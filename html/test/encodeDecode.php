<?php
$hashedPass2 = "e33496814347a5a233748adbdfa33309df2e639e413d2c1084e826a92b736374641232e97d736441c5d31579d8ea370d9df0ee3c6cf84910f2228461593b5904";

$multiplier = 2.56;
$multBy = 1;
$fullSalt = "";

while ($multBy != 51) {
    $char = $multiplier * $multBy;
    $char = round($char, 0, PHP_ROUND_HALF_UP);
    $char--;
    $fullSalt .= $hashedPass2{$char};
    $multBy++;
}

$saltNorm = $fullSalt;
$fullSalt = strrev($fullSalt);

//Salt the bitch
$i = 1;
$changedPass = $hashedPass2;
while ($i != 51) {
    $changedPass = substr_replace($changedPass, $fullSalt{$i - 1}, (round($i*2.56, 0, PHP_ROUND_HALF_UP))+ $i, 0);
    $i++;
}

//Unsalt the bitch
$i = 1;
$unSalted = $changedPass;
while ($i != 50) {
    $unSalted = substr_replace($unSalted, '', (round($i*2.56, 0, PHP_ROUND_HALF_UP)) + 1, 1);
    $i++;
}
$unSalted = substr_replace($unSalted, '', 128, 1);

$tblStr = "<tr><td>$saltNorm</td><td>$fullSalt</td></tr>";
$tblStr = "<tr><td colspan=\"2\">$hashedPass2</td></tr><tr><td colspan=\"2\">$changedPass</td></tr><tr><td colspan=\"2\">$unSalted</td></tr>" . $tblStr;
echo $changedPass;
?>