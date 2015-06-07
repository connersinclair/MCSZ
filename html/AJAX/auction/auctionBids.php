<?php
//Get the 5th highest bid
require $_SERVER["DOCUMENT_ROOT"].'/required/dbcon.php';

$sql = "SELECT auc_amount, auc_server FROM `mcsz`.`auction_2` WHERE auc_id > 0 ORDER BY auc_amount DESC";
$result = mysqli_query($con, $sql);

echo "<tr><td>#</td><td>Server name</td><td>Bid</td></tr>";
if (mysqli_num_rows($result) > 0) {
    $pos = 1;
    while($row = mysqli_fetch_assoc($result)) {
        if ($pos <= 5) {
            $tblColor = "success";
        } else {
            $tblColor = "danger";
        }
        echo "<tr class=\"$tblColor\"><td>$pos</td><td>".$row['auc_server']."</td><td>$".$row['auc_amount']."</td></tr>";
        $pos += 1;
    }
} else {
    echo "<tr><td colspan=\"3\"><center>Be the first to bid!</center></td></tr>";
}