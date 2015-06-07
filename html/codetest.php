<?php
$currHour = 0;
$hoursToGo = 24;


while ($currHour < 24) {
    $i = 0;
    while ($i < $hoursToGo) {
        $num = $currHour + $i;
        if ($num == 24) {
            $num = "00";
        } elseif ($num > 24) {
            $num = sprintf("%02d", ($num - 24));
        } else {
            $num = sprintf("%02d", $num);
        }
        
        $hoursHolder[$i] = $num . "";
        $i++;
    }
    echo "\$hour$currHour = ";
    echo str_replace("\"", "", json_encode($hoursHolder));
    echo ";<br>";
    $currHour++;
}
echo "<br>";
$currHour = 0;


while ($currHour < 24) {
    $i = 0;
    while ($i < $hoursToGo) {
        $num = $currHour + $i;
        if ($num == 24) {
            $num = "00";
        } elseif ($num > 24) {
            $num = sprintf("%02d", ($num - 24));
        } else {
            $num = sprintf("%02d", $num);
        }
        
        $hoursHolder[$i] = $num . "";
        $i++;
    }
    echo "\$hour$currHour = \"";
    echo str_replace("\"", "", json_encode($hoursHolder));
    echo "\";<br>";
    $currHour++;
}