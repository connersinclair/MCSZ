<?php

/*
Have a Cronjob execute on Sunday 00:00
Conjob will do in order:
Rename auction.php to auctionoff.php
Rename noauction.php to auction.php
Rename auctionoff.php to noauction.php

Most likely will be in some form of PHP>
Create auction with an ID
http://stackoverflow.com/questions/8334493/get-table-names-using-select-statement-in-mysql

Get table name from mysql query: select table_name from information_schema.tables;
                  vv--------------------First auction name
$auctionName = "auction_2";
$auctionID = substr($auctionName, 7);
$auctionID += 1;
    ^^----------------------------------New auction id
    
$newAuctionName = "auction_".$auctionID;

*/


/***************************************

                CHANGE
 * /AJAX/auction/auctionBids.php's
 * auction table when moving to new
 *              auction

***************************************/


require 'required/dbcon.php';

$serversArray = json_decode(file_get_contents('cronScripts/auction_users/allowed.json'), true);
$currentUser = $_COOKIE['username'];

if (!array_key_exists($currentUser, $serversArray)) {
    header("Location: servers");
}

//Reset auction table -- For testing
if ($_POST['reset'] == "reset") {
    $sql1 = "DROP TABLE `mcsz`.`auction_2`";
    $sql2 = "CREATE TABLE `mcsz`.`auction_2` ( `auc_id` INT NOT NULL AUTO_INCREMENT , `auc_server` TEXT NOT NULL , `auc_amount` TEXT NOT NULL , `auc_time` TEXT NOT NULL , PRIMARY KEY (`auc_id`) )";
    mysqli_query($con, $sql1);
    mysqli_query($con, $sql2);
}

/*
Auction Info Pane Start
*/

//Set user timezone
session_start();
if (!isset($_SESSION['timezone'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; //Get user IP...don't give a fuck about proxies
    $url = "http://freegeoip.net/json/$ip"; //Geolocation lookup
    $json = file_get_contents($url);
    $json_data = json_decode($json, true);
    $timezone = $json_data["time_zone"]; //Grab timezone from json result
    $_SESSION['timezone'] = $timezone;
}
//Set current time
ini_set("date.timezone", $_SESSION['timezone']); //Set php ini to user timezone
$currentTime = date("g:i:s A");
ini_restore("date.timezone"); //Restore for database entry consistency

//Set total bids
$sql = "SELECT auc_id FROM `mcsz`.`auction_2` ORDER BY auc_id DESC LIMIT 1";
$result = mysqli_query($con, $sql);
$result = mysqli_fetch_assoc($result);
if ($result == null) {
    $totalBids = 0;
} else {
    $totalBids = $result['auc_id'];
}

/*
Auction Info Pane End
*/


//Rest of script
$startBid = 20;
if ($_POST['postAuc']) {
    if(preg_match("/^[\d]+$/", $_POST['bid'])) {
        $sql = "SELECT auc_amount FROM `mcsz`.`auction_2` ORDER BY auc_amount DESC LIMIT 4, 1";
        $result = mysqli_query($con, $sql);
        $result = mysqli_fetch_assoc($result);
        $amount = $result['auc_amount'];
        
        if($amount == null) {
            $amount = $startBid;
        } else {
            $amount += 1;
        }
        
        if ($amount > $_POST['bid']) {
            $errorAuc = "Your bid is lower than the minimum required";
        } else {
            $timeNow = date("H:i:s");
            $bid = $_POST['bid'];
            $server = $_POST['serverName'];

            if (preg_match("/^[A-Za-z0-9\:\-\s]+$/", $server)) {
                $sql = "SELECT server_owner FROM `mcsz`.`mcsz_servers` WHERE server_name = '$server'";
                if (mysqli_num_rows(mysqli_query($con, $sql)) === 1) {
                    $sql = "INSERT INTO `mcsz`.`auction_2` VALUES ('', '$server', '$bid', '$timeNow')";
                    mysqli_query($con, $sql);
                    echo mysqli_error($con);
                    header("Location: redir?ref=auction");
                } else {
                    $errorAuc = "The selected server does not belong to this user";
                }
            } else {
                $errorAuc = "Don't mess with the server name value. It may disqualify you for future bidding events";
            }
        }
    } else {
        $sql = "SELECT auc_amount FROM `mcsz`.`auction_2` ORDER BY auc_amount DESC LIMIT 4, 1";
        $result = mysqli_query($con, $sql);
        $result = mysqli_fetch_assoc($result);
        $amount = $result['auc_amount'];
        
        if($amount == null) {
            $amount = $startBid;
        } else {
            $amount += 1;
        }
    }
} else {
    $sql = "SELECT auc_amount FROM `mcsz`.`auction_2` ORDER BY auc_amount DESC LIMIT 4, 1";
    $result = mysqli_query($con, $sql);
    $result = mysqli_fetch_assoc($result);
    $amount = $result['auc_amount'];
    
    if($amount == null) {
        $amount = $startBid;
    } else {
        $amount += 1;
    }
}
?>
<!DOCUMENT html>

<head>
    <link href="/dist/css/bootstrap-formhelpers.min.css" rel="stylesheet" />
    <link href="/required/css/chosen.min.css" rel="stylesheet">
</head>


<body class="center-block"> <!-- style="width: 75%; padding-top:20px;" -->

<?php include 'nav/nav-servers.php'; ?>
<script src="/dist/js/bootstrap-formhelpers.js"></script>
<script src="/required/js/chosen.min.js"></script>
    
<div class="container">
    <?php
    if (isset($errorAuc)) {
    ?>
    <div style="margin-top: 20px;" class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <center><?= $errorAuc ?></center>
    </div>
    <?php
    }
    ?>
    <div class="alert alert-warning" role="alert" style="text-align: center">
        <span>The least you can bid is <b>$<?= $amount ?></b></span>
    </div>
    <div id="bidContainer">
        <div class="row">
            <div class="col-md-6">
                <form id="bidForm" method="post" action="">
                    <input type="hidden" name="postAuc" value="true"/>
                    <label for="bid"><h6>Bid:</h6></label>
                    <input type="text" name="bid" class="form-control bfh-number" placeholder="Enter Bidding Amount" autocomplete="off" data-min="<?= $amount ?>" data-max="100000"><br>
                    <label for="serverName"><h6>Select a server to promote!</h6></label><br>
                    <select form="bidForm" name="serverName" class="chosen-select select-primary form-control">
                        <?php
                        $serversArray;
                        foreach ($serversArray[$currentUser] as $serverName) {
                            echo "<option value=\"$serverName\">$serverName</option>";
                        }
                        ?>
                    </select><br><br>
                    <input type="submit" class="btn btn-primary" value="Bid"/>
                </form>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Current Bids
                        <div id="tblCountdown" class="pull-right">
                            Bids refresh every 5 seconds
                        </div>
                    </div>
                    <?php
                    $sql = "SELECT * FROM `mcsz`.`auction_2` ORDER BY auc_amount DESC";
                    $result = mysqli_query($con, $sql);
                    ?>
                    <table id="auction" class="table-bordered table">
                        <tr><td colspan="3"><center>Loading current bids...</center></td></tr>
                    </table>
                </div>
                <form method="post" action="">
                    <input type="hidden" name="reset" value="reset"/>
                    <input type="submit" value="Reset Auction" class="btn btn-danger"/>
                </form>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">Auction Info</div>
                    <table class="table-bordered table">
                    <tr><td>Status</td><td><span class="text-success">Open</span></td></tr>
                    <tr><td>Auction Number</td><td>NUMBER</td></tr>
                    <tr><td>Sponsorship Length</td><td>NUMBER</td></tr>
                    <tr><td>Sponsorship Start Date</td><td>NUMBER</td></tr>
                    <tr><td>Sponsorship End Date</td><td>NUMBER</td></tr>
                    <tr><td>Total Auction Bids</td><td><?= $totalBids ?></td></tr>
                    <tr><td>Starting Bid Amount</td><td><?= "$".$startBid ?></td></tr>
                    <tr><td>Your Timezone <small class="text-muted">(automatically detected)</small></td><td><?= $_SESSION['timezone']?></td></tr>
                    <tr><td>Current Time <small class="text-muted">(based on your timezone)</small></td><td><?= $currentTime ?></td></tr>
                    <tr><td>Auction Remaining Time</td><td><span id="cntdwn" title="Not guaranteed to be accurate. Do no base bids directly off this counter without first refreshing."></span></td></tr>
                    <tr><td>Auction Start</td><td>NUMBER</td></tr>
                    <tr><td>Auction End</td><td>NUMBER</td></tr>
                    <tr><td>Payment Due Date</td><td>NUMBER</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 
<?php require "footer/footer.php"?></body>
<script>
$( document ).ready(function() {
    reloadTbl();
    $(".chosen-select").chosen();
});
function reloadTbl() {
    $.get("/AJAX/auction/auctionBids.php", function(data){
        document.getElementById("auction").innerHTML = data;
    });
    setTimeout(function(){
        reloadTbl();
    }, 5000);
}
TargetDate = "12/31/2020 0:00 AM";
BackColor = "white";
ForeColor = "black";
DisplayFormat = "H:M:S";
FinishMessage = "";
</script>
<script language="JavaScript" src="/required/js/countdown.js"></script>