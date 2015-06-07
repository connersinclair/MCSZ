<?php

#############
die();#######
#############

$key = "";

$i = 1;
$unSalted = $key;
while ($i != 50) {
    $unSalted = substr_replace($unSalted, '', (round($i*2.56, 0, PHP_ROUND_HALF_UP)) + 1, 1);
    $i++;
}
$unSalted = substr_replace($unSalted, '', 128, 1);
echo $unSalted;