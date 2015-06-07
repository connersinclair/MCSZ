<?php

error_reporting(E_ALL & ~E_NOTICE);

//Run cron job with a DROP SQL command for all votes older than 28 days\
function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['0'] === $id) {
           return $key;
       }
   }
   return "Not Ranked";
}

function HoursArray($currentHour) {
    //Used in as an array
    $hour0  = [00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23];
    $hour1  = [01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00];
    $hour2  = [02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01];
    $hour3  = [03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02];
    $hour4  = [04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03];
    $hour5  = [05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04];
    $hour6  = [06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05];
    $hour7  = [07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06];
    $hour8  = [08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07];
    $hour9  = [09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08];
    $hour10 = [10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09];
    $hour11 = [11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10];
    $hour12 = [12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11];
    $hour13 = [13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12];
    $hour14 = [14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13];
    $hour15 = [15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14];
    $hour16 = [16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15];
    $hour17 = [17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16];
    $hour18 = [18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17];
    $hour19 = [19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18];
    $hour20 = [20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19];
    $hour21 = [21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20];
    $hour22 = [22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21];
    $hour23 = [23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22];
    
    return ${"hour". $currentHour};
}

function hoursArrayString($currentHour) {
    //Used as a string to echo
    $hour0 = "[00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23]";
    $hour1 = "[01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00]";
    $hour2 = "[02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01]";
    $hour3 = "[03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02]";
    $hour4 = "[04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03]";
    $hour5 = "[05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04]";
    $hour6 = "[06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05]";
    $hour7 = "[07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06]";
    $hour8 = "[08,09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07]";
    $hour9 = "[09,10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08]";
    $hour10 = "[10,11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09]";
    $hour11 = "[11,12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10]";
    $hour12 = "[12,13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11]";
    $hour13 = "[13,14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12]";
    $hour14 = "[14,15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13]";
    $hour15 = "[15,16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14]";
    $hour16 = "[16,17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15]";
    $hour17 = "[17,18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16]";
    $hour18 = "[18,19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17]";
    $hour19 = "[19,20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18]";
    $hour20 = "[20,21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19]";
    $hour21 = "[21,22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20]";
    $hour22 = "[22,23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21]";
    $hour23 = "[23,00,01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20,21,22]";

    return ${"hour". $currentHour};
}

function votifier($public_key, $server_ip, $server_port, $username, $user_ip) 
{

// Details of the vote.
$str = "VOTE\n" .
      "MCSZ\n" .
      "$username\n" .
      "$user_ip\n" .
      time()."\n";

// Fill in empty space to make the encrypted block 256 bytes.
$leftover = (256 - strlen($str)) / 2;

while ($leftover > 0) {
    $str .= "\x0";
    $leftover--;
}

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
	fwrite($socket, $encrypted); 	//on success send encrypted packet to server
	return true;
}
else
	return false; //on fail return false
}

/**
 * @param $svrid
 */
function getServer($svrid) {
    require 'required/dbcon.php';
    if ($con == false) {
        echo "Please come back later, our voting system is currently down.";
    } else {
        if (!isset($svrid) || !preg_match("/^[0-9]+$/", $svrid)) {
            ?>
            <center>Redirecting to front page...Invalid VoteID supplied</center>
            <script>
                window.location.replace("/");
            </script>
            <?php
        } else {
            $voteid = $svrid;
            $sql = "SELECT server_id FROM `mcsz`.`mcsz_servers` WHERE server_id = '$voteid' LIMIT 1";
            $result = mysqli_query($con, $sql);
            $result = mysqli_fetch_assoc($result);
            if ($result != null) {
                $sql = "SELECT * FROM `mcsz`.`mcsz_servers` WHERE server_id = '$voteid' LIMIT 1";
                $result = mysqli_query($con, $sql);
                $result = mysqli_fetch_assoc($result);
                
                $rankings = file_get_contents("cronScripts/server_rankings/ranking.json");
                $ranks = json_decode($rankings, true);
                $currRank = searchForId($voteid, $ranks);
                
                $countryArray = array("AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia, Plurinational State of", "BQ" => "Bonaire, Sint Eustatius and Saba", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "IO" => "British Indian Ocean Territory", "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos (Keeling) Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo", "CD" => "Congo, the Democratic Republic of the", "CK" => "Cook Islands", "CR" => "Costa Rica", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands (Malvinas)", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and McDonald Islands", "VA" => "Holy See (Vatican City State)", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran, Islamic Republic of", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KP" => "Korea, Democratic People's Republic of", "KR" => "Korea, Republic of", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Lao People's Democratic Republic", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macao", "MK" => "Macedonia, the former Yugoslav Republic of", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "MX" => "Mexico", "FM" => "Micronesia, Federated States of", "MD" => "Moldova, Republic of", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territory, Occupied", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda", "SH" => "Saint Helena, Ascension and Tristan da Cunha", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin (French part)", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "ST" => "Sao Tome and Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SX" => "Sint Maarten (Dutch part)", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "SS" => "South Sudan", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syrian Arab Republic", "TW" => "Taiwan, Province of China", "TJ" => "Tajikistan", "TZ" => "Tanzania, United Republic of", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "UM" => "United States Minor Outlying Islands", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VE" => "Venezuela, Bolivarian Republic of", "VN" => "Viet Nam", "VG" => "Virgin Islands, British", "VI" => "Virgin Islands, U.S.", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe");
                
                $country = $result['server_country'];
                $country = $countryArray[$country];
                $description = $result['server_description'];
                $svrName = $result['server_name'];

                $serverBanner = $result['server_bannerLocation'];
                $_POST['serverName'] = $svrName;
                ?>
                
                <script>
                    $(document).ready(function() {
                        var getLink = "server=<?=$result['server_ip']?>&port=<?=$result['server_port']?>";
                        $.get("/AJAX/serverPingVotePage/pingtest.php?" + getLink, function(data) {
                            var jsonData = JSON.parse(data);
                            if (jsonData.success == true) {
                                $("#serverStatus").html("<span class=\"text-success\">Online</span>");
                                $("#serverPlayers").html(jsonData.players + "/" + jsonData.max);
                            } else if(jsonData.success == false) {
                                $("#serverStatus").html("<span class=\"text-danger\">Offline</span>");
                                $("#serverPlayers").html(jsonData.players + "/" + jsonData.max);
                            }
                        });
                    });
                </script>
                
                <div class="container"> 
                    <div id="bidContainer">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="panel panel-primary">
									<div class="panel-heading"><?= $svrName;?></div>
		                            <table class="table-bordered table">
                                    <tr><td style="width:135px">Server Owner:</td><td><?= $result['server_owner'];?></td></tr>
                                    <tr><td style="width:135px">Server Ranking:</td><td><?= $currRank;?></td></tr>
                                    <tr><td style="width:135px">Server IP:</td><td><?= $result['server_ip'];?></td></tr>
									<?php
                                        $websiteLength = strlen($result['server_website']);
                                        if ($websiteLength == 0) {
                                            $url = "#";
                                            $webName = "None";
                                        } else {
                                            if (substr( $result['server_website'], 0, 7 ) === "http://") {
                                                $url = $result['server_website'];
                                                $webName = $result['server_website'];
                                            } else {
                                                if (substr( $result['server_website'], 0, 8 ) === "https://") {
                                                    $url = $result['server_website'];
                                                    $webName = $result['server_website'];
                                                } else {
                                                    $url = "http://" . $result['server_website'];
                                                    $webName = $result['server_website'];
                                                }
                                            }
                                        }
                                    ?>
									<tr><td style="width:135px">Server Website:</td><td><a href="<?= $url?>"><?= $webName?></a></td></tr>
                                    <tr><td style="width:135px">Server Port:</td><td><?= $result['server_port'];?></td></tr>
                                    <tr><td style="width:135px">Server Country:</td><td><?= $country?></td></tr>
                                    <tr><td style="width:135px">Current Status:</td><td><div id="serverStatus">Loading Status...</div></td></tr>
                                    <tr><td style="width:135px">Current Players:</td><td><div id="serverPlayers">Loading Players...</div></td></tr>
                                    </table>
                                </div>
                                
                                <?php 
                                if (isset($_COOKIE['username'])) {
                                    ?>
                                    <?php $serverInfo = array_merge($result);
                                    $sql = "SELECT vote_id FROM `mcsz`.`mcsz_votes` WHERE vote_server = '$voteid'";
                                    $result = mysqli_query($con, $sql);
                                    $result = mysqli_num_rows($result);
                                    ?>
                                    <h2 style="text-align: left">Vote for <?= $serverInfo['server_name'];?>!</h2>
                                
                                        <?= "This server has a total of ".$result." votes!";?><br><br>
                                        <div style="width: 250px; display: inline-block">
                                            <form method="post" action="">
                                                <label for="username">Your In-Game Username</label>
                                                <input type="text" class="form-control" maxlength="16" name="username"><br>
                                                <input type="hidden" name="vote" value="true">
                                                <input type="submit" class="btn btn-primary" value="Vote">
                                            </form>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <h5 style="text-align: center">Please login to vote for this server</h5>
                                        <?php
                                    }
                                    ?>  
                            </div>
                            <div class="col-md-8">
                                <div class="panel panel-primary" id="serverinfo">
                                    <div class="panel-heading">
                                        <ul class="vote-ul">
                                            <li id="vote-info">Info</li>
                                            <li id="vote-stats">Stats</li>
                                        </ul>
                                    </div>
                                    <div class="panel-body">
                                        <div id="serverVoteDesc" class="">
                                            <?php
                                            $bannerLen = strlen($serverBanner);
                                            if ($bannerLen < 4) {
                                                $banLoc = "/static/nobanner.png";
                                            } else {
                                                $serverBanner = "banners/".$serverBanner;
                                                if (!file_exists($serverBanner)) {
                                                    echo $serverBanner;
                                                    $banLoc = "/static/nobanner.png";
                                                } else {
                                                    $banLoc = "/".$serverBanner;
                                                }
                                            }
                                            ?>
                                            <center><img class="descpic" src="<?=$banLoc?>"></center>
                                            <p class="desc"><?= $description;?></p>
                                        </div>
                                        <div id="serverStatCharts" class="hidden">
                                            <canvas id="serverStatsTabbed" height="400"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                    //Calculate Hours on X axis

                                    //Set current hour
                                    $currHour = date("H");
                                    //Set current date/hour
                                    $currentDate = date("m/d/Y H");

                                    //Convert to integer for safety
                                    $currHour = (int)$currHour;

                                    if ($currHour == 24) {
                                        $currHour = 0;
                                    } else {
                                        $currHour++;
                                    }

                                    //Get string to display for the labels
                                    $hoursArrayString = hoursArrayString($currHour);

                                    //Get hours actual array
                                    $currentHoursArray = HoursArray($currHour);

                                    //$sql = "SELECT vote_date, COUNT(*) FROM `c9`.`mcsz_votes` WHERE vote_server = '$voteid' AND vote_date >= ( CURDATE() - INTERVAL 1 DAY )";
                                    //vvv Experimental code
                                    $sql = "SELECT vote_date FROM `mcsz`.`mcsz_votes` WHERE vote_server = '$voteid' AND vote_date >= ( CURDATE() - INTERVAL 1 DAY )";

                                    $voteData = mysqli_query($con, $sql);

                                    $loggedHour = [];
                                    foreach ($voteData as $server) {
                                        $loggedHour[(int)substr($server['vote_date'], -8, 2)] += 1;
                                    }

                                    $dataString = "[";
                                    for($i = 0; $i <= 24; $i++){
                                        $checkHour = $currentHoursArray[$i];
                                        if($loggedHour[$checkHour] == null) {
                                            $dataString .= "0,";
                                        } else {
                                            $dataString .= $loggedHour[$checkHour].",";
                                        }
                                    }
                                    $dataString = substr($dataString, 0, -1);
                                    $dataString .= "]";

                                    $_POST["hoursArrayString"] = $hoursArrayString;
                                    $_POST["dataString"] = $dataString;
                                    ?>

                                <table class="table table-striped">
                                    <tr><th>#</th><th>IGN</th><th>Votes</th></tr>
                                    <?php
                                    $sql = "SELECT vote_user, COUNT(*) FROM `mcsz`.`mcsz_votes` WHERE vote_server = '$voteid' GROUP BY vote_user ORDER BY COUNT(*) DESC LIMIT 5";
                                    $voteRanks = mysqli_query($con, $sql);
                                    
                                    if (mysqli_num_rows($voteRanks) == 0) {
                                        ?>
                                        <tr><td colspan="3">This server has no votes :(</td></tr>
                                        <?php
                                    } else {
                                        $pos = 1;
                                        while ($result = mysqli_fetch_assoc($voteRanks)) {
                                            ?>
                                            <tr><td><?= $pos?></td><td><?= $result['vote_user']?></td><td><?= $result['COUNT(*)']?></td></tr>
                                            <?php
                                            $pos++;
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>   
                        
                    </div>
                </div>
                </br>
            <?php
            } else {
                echo "That server ID does not exist";
            }
        }
    }
}

if ($_POST['vote']) {
    require 'required/dbcon.php';
    $usr  = $_POST['username'];
    $vtid = $_GET['serverid'];
    if (preg_match("/^[A-Za-z0-9\_]+$/", $usr)) {
        $usrLength = strlen($usr);
        if ($usrLength >= 2 ) {
            if (preg_match("/^[0-9]+$/", $vtid)) {
                require 'required/dbcon.php';
                $dateNow = date("Y-m-d H:i:s");
                $userIp  = $_SERVER['REMOTE_ADDR'];
                $voteAccount = $_COOKIE['username'];
                $sql = "SELECT vote_id FROM `mcsz`.`mcsz_votes` WHERE (vote_server = '$vtid' AND vote_date > '$dateNow' - INTERVAL 1 DAY) AND (vote_user = '$usr' OR vote_ip = '$userIp' OR vote_account = '$voteAccount')";
                $voteCnt = mysqli_query($con, $sql);
                if (mysqli_num_rows($voteCnt) == 0) {
                    $sql = "INSERT INTO `mcsz`.`mcsz_votes` (vote_id, vote_user, vote_account, vote_ip, vote_server, vote_date) VALUES ('', '$usr', '$voteAccount', '$userIp', '$vtid', '$dateNow')";
                    if (mysqli_query($con, $sql)) {
                        $checkUseVotifier = "SELECT server_ip, server_use_votifier, server_votifier_port, server_public_key FROM `mcsz`.`mcsz_servers` WHERE server_id = '$vtid'";
                        $useVotifier = mysqli_query($con, $checkUseVotifier);
                        $useVotifier = mysqli_fetch_assoc($useVotifier);
                        if ($useVotifier['server_use_votifier'] == 1) {
                            $votifierIP   = $useVotifier['server_ip'];
                            $votifierPort = $useVotifier['server_votifier_port'];
                            $votifierKey  = $useVotifier['server_public_key'];
                            if (votifier($votifierKey, $votifierIP, $votifierPort, $usr, $userIp)) {
                                header("Location: /vote/$vtid/success");
                            } else {
                                $error = "This server has Votifier enabled on the site, but our server could not connect to their service. Your vote has been registered on the site";
                            }
                        } else {
                            header("Location: /vote/$vtid/success");
                        }
                    } else {
                        $error = "Something on our end prevented you from voting. Sorry about the inconvenience";
                    }
                } else {
                    $error = "You have already voted today for this server";
                }
            } else {
                $error = "The server ID in the URL can only contain numbers";
            }
        } else {
            $error = "Your username needs to be longer than 2 characters";
        }
    } else {
        $error = "That username doesn't fit the Minecraft username criteria";
    }
}

if (isset($_GET['success'])) {
	$success = "Your vote was successfully sent to the server";
}
$vtid = $_GET['serverid'];
?>
<html>
    <head>
        <title>Vote for your favourite server | MCSZ</title>
    </head>
    <body>
        <?php require 'nav/nav-servers.php';?>
        <!-- Load Chart.js resources -->
        <script src="/chartjs/chart.min.js"></script>

        
        <div class="container" style="word-wrap: break-word; margin-top: 20px;">
            <?php 
			
			if (isset($_GET['success'])) {
			?>
			<script>
			$(document).ready(function() {
				window.history.pushState('Vote for <?= $vtid ?>', 'Vote for <?= $vtid ?>', '/vote/<?= $vtid ?>');
			});
			</script>
			<?php
			}
			
            getServer($_GET['serverid']);
            
            if ($_POST['voteLogin'] == true) {
                ?>
                <script>
                    $( document ).ready(function() {
                        $('#loginModal').modal('toggle');
                    });
                </script>
                <?php
            }
            ?>
        </div>
        <script>
            $("#vote-info").click(function() {
                //serverStatCharts
                $("#serverStatCharts").addClass("hidden");
                $("#serverVoteDesc").removeClass("hidden");

            });
            $("#vote-stats").click(function() {
                $("#serverStatCharts").removeClass("hidden");
                $("#serverVoteDesc").addClass("hidden");
            });
        </script>
        <script>
            var first = 1;

            $("#vote-stats").click(function() {
                if (first == 1) {
                    first = 0;
                    var canvasWidth = $("#serverinfo").width() - 15;
                    $("#serverStatsTabbed").attr("width", canvasWidth);

                    var lastHours = new Date();
                    var currentHour = lastHours.getHours();

                    var statData = {
                        labels : <?= $_POST["hoursArrayString"]?>,
                        datasets : [
                            {
                                fillColor : "rgba(172,194,132,0.4)",
                                strokeColor : "#ACC26D",
                                pointColor : "#fff",
                                pointStrokeColor : "#9DB86D",
                                data : <?=$_POST["dataString"]?>
                            }
                        ]
                    };
                    var stats = document.getElementById('serverStatsTabbed').getContext('2d');
                    new Chart(stats).Line(statData, {
                        showTooltips: false
                    });
                }
            });
            $(document).ready(function() {
                document.title = 'Vote for <?=$_POST['serverName']?> | MCSZ';
            });
        </script>
        
</html>
    <?php require "footer/footer.php"?>