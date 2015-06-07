<?php

if (!isset($_COOKIE['username'])) {
    header("Location: servers?ref=login");
}

function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['0'] === $id) {
           return $key;
       }
   }
   return "Not Ranked";
}

function delServer($svrid, $username) {
    require 'required/dbcon.php';
    if (preg_match("/^[0-9]+$/", $svrid)) {
        $sql = "SELECT server_bannerLocation FROM `mcsz`.`mcsz_servers` WHERE server_owner = '$username' AND server_id = '$svrid'";
        $result = mysqli_query($con, $sql);
        $resultArray = mysqli_fetch_assoc($result);
        $banner = $resultArray['server_bannerLocation'];
        if (mysqli_num_rows($result) == 1) {
            unlink("banners/".$banner);
            $sql = "DELETE FROM `mcsz`.`mcsz_servers` WHERE `server_id` = '$svrid'";
            
            if (mysqli_query($con, $sql)) {
                $sql = "INSERT INTO `mcsz`.`mcsz_votes_removed` (vote_user, vote_ip, vote_server, vote_date) SELECT vote_user, vote_ip, vote_server, vote_date FROM `mcsz`.`mcsz_votes` WHERE vote_server = '$svrid'";
                if (mysqli_query($con, $sql)) {
                    $sql = "DELETE FROM `mcsz`.`mcsz_votes` WHERE `vote_server` = '$svrid'";
                    if (mysqli_query($con, $sql)) {
                        header("Location: redir.php?ref=myservers");
                    } else {
                        $error = "There was an error removing the votes from our database, but the server was successfully deleted";
                    }
                } else {
                    $error = "There was an error removing the votes from our database, but the server was successfully deleted";
                }
            } else {
                $error = "There was an error removing your server from our database";
            }
        } else {
            $error = "An error occured. That server has already been deleted or something of the like";
        }
    } else {
        $error = "You seem to have fucked around with the ID of the deletion form";
    }
}

if (isset($_POST['delete'])) {
    $username = $_COOKIE['username'];
    $id = $_POST['serverid'];
    delServer($id, $username);
}

?>

<html>

<head>
    <?php require 'nav/nav-servers.php'?>
    <title>My Resources | MCSZ</title>
</head>

<body>
    <div style="margin-top: 20px; word-wrap: break-word;" class="container">
        <?php 
        if (isset($error)) {
            echo $error;
        }
        
        require 'required/dbcon.php';
        $username = $_COOKIE['username'];
        $sql = "SELECT * FROM `mcsz`.`mcsz_servers` WHERE server_owner = '$username'";
        $result = mysqli_query($con, $sql);
        
        $countryArray = array("AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia, Plurinational State of", "BQ" => "Bonaire, Sint Eustatius and Saba", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "IO" => "British Indian Ocean Territory", "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos (Keeling) Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo", "CD" => "Congo, the Democratic Republic of the", "CK" => "Cook Islands", "CR" => "Costa Rica", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands (Malvinas)", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and McDonald Islands", "VA" => "Holy See (Vatican City State)", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran, Islamic Republic of", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KP" => "Korea, Democratic People's Republic of", "KR" => "Korea, Republic of", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Lao People's Democratic Republic", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macao", "MK" => "Macedonia, the former Yugoslav Republic of", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "MX" => "Mexico", "FM" => "Micronesia, Federated States of", "MD" => "Moldova, Republic of", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territory, Occupied", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda", "SH" => "Saint Helena, Ascension and Tristan da Cunha", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin (French part)", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "ST" => "Sao Tome and Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SX" => "Sint Maarten (Dutch part)", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "SS" => "South Sudan", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syrian Arab Republic", "TW" => "Taiwan, Province of China", "TJ" => "Tajikistan", "TZ" => "Tanzania, United Republic of", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "UM" => "United States Minor Outlying Islands", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VE" => "Venezuela, Bolivarian Republic of", "VN" => "Viet Nam", "VG" => "Virgin Islands, British", "VI" => "Virgin Islands, U.S.", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe");
        
        ?>
            <table style="width:100%; table-layout: fixed; border-spacing: 5px;border-collapse: separate;">
                                                <!-- Why the fuck this not ^^^ work -->
                <tr>
                <?php
                $i = 0;
                foreach($result as $btnServer) {
                    if ($i >= 3) {
                        ?><tr><?php
                        $i = 0;
                        ?>
                        <td>
                        <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#collapse<?=$btnServer['server_id']?>" aria-expanded="false" aria-controls="collapse<?=$btnServer['server_id']?>">
                            <?= $btnServer['server_name'] ?>
                        </button>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td>
                        <button class="btn btn-primary btn-block" type="button" data-toggle="collapse" data-target="#collapse<?=$btnServer['server_id']?>" aria-expanded="false" aria-controls="collapse<?=$btnServer['server_id']?>">
                            <?= $btnServer['server_name'] ?>
                        </button>
                        </td>
                        <?php
                    }
                    $i++;
                }
                ?>
                </tr>
            </table>
        <br>
        <?php
        
        foreach ($result as $server) {
            
            $rankings = file_get_contents("cronScripts/server_rankings/ranking.json");
            $ranks = json_decode($rankings, true);
            $currRank = searchForId($server['server_id'], $ranks);
            
            $country = $server['server_country'];
            $country = $countryArray[$country];
            
            $serverID = $server['server_id'];
            $sql = "SELECT vote_id FROM `mcsz`.`mcsz_votes` WHERE vote_server = '$serverID'";
            $result = mysqli_query($con, $sql);
            $totalVotes = mysqli_num_rows($result);
            ?>
            <div class="collapse" id="collapse<?=$server['server_id']?>"> 
                <div class="well">
                    <!--<h5><?= $server['server_name'];?></h5>-->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="panel panel-primary"> 
                                <div class="panel-heading"><?= $server['server_name'];?></div>
                                <table class="table-bordered table">
                                    <tr><td style="width:135px">Server Name:</td><td><?= $server['server_name'];?></td></tr> 
                                    <tr><td style="width:135px">Server Owner:</td><td><?= $server['server_owner'];?></td></tr> 
                                    <tr><td style="width:135px">Server Ranking:</td><td><?= $currRank;?></td></tr> 
                                    <tr><td style="width:135px">Server IP:</td><td><?= $server['server_ip'];?></td></tr> 
                                    <tr><td style="width:135px">Server Port:</td><td><?= $server['server_port'];?></td></tr> 
                                    <tr><td style="width:135px">Server Website:</td><td><?= $server['server_website'];?></td></tr> 
                                    <tr><td style="width:135px">Server Country:</td><td><?= $country?></td></tr> 
                                    <tr><td style="width:135px">Current Status:</td><td><div id="serverStatus<?=$server['server_id']?>">Loading Status...</div></td></tr> 
                                    <tr><td style="width:135px">Current Players:</td><td><div id="serverPlayers<?=$server['server_id']?>">Loading Players...</div></td></tr> 
                                    <tr><td style="width:135px">Total Votes:</td><td><div><?=$totalVotes?></div></td></tr> 
                                </table>
                            </div><hr>
                        </div>
                        <div class="col-md-8">
                            <div class="panel panel-primary" style="height:442px;">
                                <div class="panel-heading">Server Description</div>
                                <?php
                                if (strlen($server['server_bannerLocation']) < 4) {
                                    $server['server_bannerLocation'] = "/static/nobanner.png";
                                } else {
                                    $server['server_bannerLocation'] = "banners/".$server['server_bannerLocation'];
                                    if (!file_exists($server['server_bannerLocation'])) {
                                        $server['server_bannerLocation'] = "/static/nobanner.png";
                                    } else {
                                        $server['server_bannerLocation'] = "/".$server['server_bannerLocation'];
                                    }
                                }
                                ?>
                                <center><a href="/vote/<?= $serverID?>"><img class="descpic" src="<?= $server['server_bannerLocation']?>"></img></a></center>
                                <p class="desc"><?= $server['server_description'];?></p>
                            </div><hr>
                        </div>
                    </div>
                    <table width="100%" style="table-layout: fixed;">
                        <tr>
                            <td align="left"><a class="btn btn-danger" id="fakeDeleteBtn<?=$server['server_id']?>">Delete This Server</a></td>
                            <td align="center"><a class="btn btn-primary" href="/vote/<?=$server['server_id']?>">See Your Vote Page!</a></td>
                            <td align="right"><a class="btn btn-inverse" href="/editserver/<?=$server['server_id']?>">Edit Server</a></td>
                        </tr>
                    </table>
                    <div class="hidden">
                        <form class="form-inline" method="post" id="delSvr<?=$server['server_id']?>" action=""><input type="hidden" value="<?=$server['server_id']?>" name="serverid"><input type="hidden" value="delete" name="delete"><button id="delForm<?=$server['server_id']?>" form="delSvr<?=$server['server_id']?>" class="btn btn-danger">Delete</button></form>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    var getLink = "server=<?=$server['server_ip']?>&port=<?=$server['server_port']?>";
                    $.get("/AJAX/serverPingVotePage/pingtest.php?" + getLink, function(data) {
                        var jsonData = JSON.parse(data);
                        if (jsonData.success == true) {
                            $("#serverStatus<?=$server['server_id']?>").html("<span class=\"text-success\">Online</span>");
                            $("#serverPlayers<?=$server['server_id']?>").html(jsonData.players + "/" + jsonData.max);
                        } else if(jsonData.success == false) {
                            $("#serverStatus<?=$server['server_id']?>").html("<span class=\"text-danger\">Offline</span>");
                            $("#serverPlayers<?=$server['server_id']?>").html(jsonData.players + "/" + jsonData.max);
                        }
                    });
                });
                $("#fakeDeleteBtn<?=$server['server_id']?>").click(function() {
                   $("#delForm<?=$server['server_id']?>").trigger("click"); 
                });
            </script>
            <?php
        }
        ?>
    </div>
    
<?php require "footer/footer.php"?></body>

</html>