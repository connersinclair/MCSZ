<?php

require 'required/dbcon.php';

if (!isset($_COOKIE['username'])) {
    header("Location: servers?ref=login");
}

$username = $_COOKIE['username'];

//Lets go down the if tree so see if we can add a server :D
$sql = "SELECT server_id FROM `mcsz`.`mcsz_servers` WHERE server_owner = '$username'";
$result = mysqli_query($con, $sql);

$maxServers = 3;

if (mysqli_num_rows($result) >= $maxServers) {
    $tooMany = true;
} else {
    if (mysqli_num_rows($result) < 1) {
        $left = "<h3>Add your first server!</h3><br><br>";
    } else {
        $left = "You can add " . ($maxServers - mysqli_num_rows($result)) . " more server(s)<br><br>";
    }
}

if (isset($_POST['serverName'])) {
    //Check server name for alphanumerical and length
    $strlen = strlen($_POST['serverName']);
    $uploadOk = 1;
    if ($strlen <= 20 && $strlen > 5) {
        if (preg_match("/^[A-Za-z0-9\:\-\s]+$/", $_POST['serverName'])) {
            $serverPostName = $_POST['serverName'];
            $sql    = "SELECT server_owner FROM `mcsz`.`mcsz_servers` WHERE server_name = '$serverPostName'";
            $result = mysqli_query($con, $sql);
            $result = mysqli_fetch_assoc($result);
            if ($result['server_owner'] == null) {
                $serverPostIP = $_POST['serverIP'];
                $sql    = "SELECT server_owner FROM `mcsz`.`mcsz_servers` WHERE server_ip = '$serverPostIP'";
                $result = mysqli_query($con, $sql);
                $result = mysqli_fetch_assoc($result);
                if ($result['server_owner'] == null) {
                   //Check server description for the same thing
                    $strlen = strlen($_POST['serverDescription']);
                    if ($strlen <= 1000) {
                        if ($strlen >= 50) {
                            if (preg_match("/^[A-Za-z0-9\s\:\\\\\/\.\'\-\*\!\,]+$/", $_POST['serverDescription'])) {
                                if (isset($_POST['fileToUpload'])) {
									ini_set("upload_tmp_dir", "/var/www/html/tempbanners/" );

                                    $target_dir = "banners/";
                                    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                                    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                                    $image_info = getimagesize($_FILES["file_field_name"]["tmp_name"]);
                                    $uploadOk = 1;
                                    // Check if image file is a actual image or fake image
                                    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                                    //Check image type...will have to do some testing with this one
                                    if($check['mime'] == "image/gif") {
                                        // Check if file already exists
                                        if (!file_exists($target_file)) {
                                            // Check file size
                                            if ($_FILES["fileToUpload"]["size"] <= 1000000) {
                                                // Check image dimensions
                                                if ($check['0'] != 468 && $check['1'] != 60) {
                                                    $uploadOk = 0;
                                                    $error =  "Sorry, file dimensions must be 468x60";
                                                }
                                            } else {
                                                $uploadOk = 0;
                                                $error =  "Sorry, your file is too large.";
                                            }
                                        } else {
                                            $uploadOk = 0;
                                            $error =  "Sorry, file already exists.";
                                        }
                                    } else {
                                        $uploadOk = 0;
                                        $error =  "Sorry, the uploaded file is not a GIF.";
                                    }
                                }
                            } else {
                                $uploadOk = 0;
                                $error = "Server descriptions can only contain <span title=\"English letters and numbers\">alphanumeric characters</span> plus spaces";
                            }
                        } else {
                            $uploadOk = 0;
                            $error = "Server description has to be at least 50 characters";
                        }
                    } else {
                        $uploadOk = 0;
                        $error = "Your server description must be less than 1000 characters";
                    } 
                } else {
                    $uploadOk = 0;
                    $error = "That server IP already exists in our database";
                }
            } else {
                $uploadOk = 0;
                $error = "That server name already exists in our database";
            }
        } else {
            $uploadOk = 0;
            $error = "Server names can only contain <span title=\"English letters and numbers\">alphanumeric characters</span> plus spaces";
        }
    } else {
        if ($strlen >= 40) {
            $uploadOk = 0;
            $error = "Your server name cannot be longer than 40 characters";
        } elseif ($strlen <= 5) {
            $uploadOk = 0;
            $error = "Your server name must be longer than 5 characters";
        } else {
            $uploadOk = 0;
            $error = "Please enter a valid server name";
        }
    }
    if ($_POST['useVotifier'] == "on") {
        if (preg_match("/^MII[0-9A-Za-z+\/]+[=]{0,3}+$/", $_POST['votifierPubKey'])) {
            if (!preg_match("/^[0-9]+$/", $_POST['votifierPort'])) {
                $uploadOk = 0;
                $error = "Your votifier port can only contain numbers";
            }
        } else {
            $uploadOk = 0;
            $error = "Your RSA Key is not in a valid format";
            
        }
    }
    $countryArray = array("AF", "AL", "DZ", "AS", "AD", "AO", "AI", "AQ", "AG", "AR", "AM", "AW", "AU", "AT", "AZ", "BS", "BH", "BD", "BB", "BY", "BE", "BZ", "BJ", "BM", "BT", "BO", "BQ", "BA", "BW", "BV", "BR", "IO", "BN", "BG", "BF", "BI", "KH", "CM", "CA", "CV", "KY", "CF", "TD", "CL", "CN", "CX", "CC", "CO", "KM", "CG", "CD", "CK", "CR", "HR", "CU", "CY", "CZ", "DK", "DJ", "DM", "DO", "EC", "EG", "SV", "GQ", "ER", "EE", "ET", "FK", "FO", "FJ", "FI", "FR", "GF", "PF", "TF", "GA", "GM", "GE", "DE", "GH", "GI", "GR", "GL", "GD", "GP", "GU", "GT", "GG", "GN", "GW", "GY", "HT", "HM", "VA", "HN", "HK", "HU", "IS", "IN", "ID", "IR", "IQ", "IE", "IM", "IL", "IT", "JM", "JP", "JE", "JO", "KZ", "KE", "KI", "KP", "KR", "KW", "KG", "LA", "LV", "LB", "LS", "LR", "LY", "LI", "LT", "LU", "MO", "MK", "MG", "MW", "MY", "MV", "ML", "MT", "MH", "MQ", "MR", "MU", "YT", "MX", "FM", "MD", "MC", "MN", "ME", "MS", "MA", "MZ", "MM", "NA", "NR", "NP", "NL", "NC", "NZ", "NI", "NE", "NG", "NU", "NF", "MP", "NO", "OM", "PK", "PW", "PS", "PA", "PG", "PY", "PE", "PH", "PN", "PL", "PT", "PR", "QA", "RO", "RU", "RW", "SH", "KN", "LC", "MF", "PM", "VC", "WS", "SM", "ST", "SA", "SN", "RS", "SC", "SL", "SG", "SX", "SK", "SI", "SB", "SO", "ZA", "GS", "SS", "ES", "LK", "SD", "SR", "SJ", "SZ", "SE", "CH", "SY", "TW", "TJ", "TZ", "TH", "TL", "TG", "TK", "TO", "TT", "TN", "TR", "TM", "TC", "TV", "UG", "UA", "AE", "GB", "US", "UM", "UY", "UZ", "VU", "VE", "VN", "VG", "VI", "WF", "EH", "YE", "ZM", "ZW");
    if (!in_array($_POST['serverCountry'], $countryArray)) {
        $_POST['serverCountry'] = "US";
    }
    if (preg_match("/^[A-Za-z0-9\-\.]+$/", $_POST['serverIP'])) {
        if (substr_count($_POST['serverIP'], ".") > 1) {
            $strlen = strlen($_POST['serverIP']);
            if ($strlen > 35) {
                $uploadOk = 0;
                $error = "Please use an IP that is less than 35 characters";
            }
        } else {
            $upload = 0;
            $error = "Your IP must contain at least one period";
        }
    } else {
        $uploadOk = 0;
        $error = "IP's can only contain <span title=\"English letters and numbers\">alphanumeric characters</span> plus dashes and periods";
    }
    if (isset($_POST['serverWebsite'])) {
        if (preg_match("/^[A-Za-z0-9\/\:\-\.]+$/", $_POST['serverWebsite'])) {
            if (substr_count($_POST['serverWebsite'], ".") >= 1) {
                $strlen = strlen($_POST['serverWebsite']);
                if ($strlen > 35) {
                    $uploadOk = 0;
                    $error = "Please use a URL that is less than 35 characters";
                }
            } else {
                $upload = 0;
                $error = "Your URL must contain at least 1 period";
            }
        } else {
            $uploadOk = 0;
            $error = "Website URL's can only contain <span title=\"English letters and numbers\">alphanumeric characters</span> plus dashes and periods";
        }
    }
    if ($uploadOk == 1) {
        if ($_FILES['fileToUpload']['name'] != null) {
            $target_dir = "/var/www/html/banners/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $newName = time() . $_COOKIE['username'];
                rename($target_file, $target_dir.$newName.".gif");
                
                $serverName      = $_POST['serverName'];
                $serverDesc      = $_POST['serverDescription'];
                $serverIP        = $_POST['serverIP'];
                $serverCountry   = $_POST['serverCountry'];
                $serverBannerLoc = $newName.".gif";
                $serverTime      = date("H:i:s d/m/Y");
                $serverWebsite  = $_POST['serverWebsite'];
                $serverBannerLoc = $newName.".gif";
                
                if ($_POST['userVotifier'] == "on") {
                    $serverVotifierPort = $_POST['votifierPort'];
                    $serverVotifierPKey = $_POST['votifierPubKey'];
                    $serverUseVotifier  = 1;
                } else {
                    $serverVotifierPort = "";
                    $serverVotifierPKey = "";
                    $serverUseVotifier  = 0;
                }
                
                if ($_POST['portCheck'] == "on") {
                    $serverPort     = $_POST['portNum'];
                    if ($_POST['portNumCheck'] == "on") {
                        $serverPortShow = 1;
                    } else {
                        $serverPortShow = 0;
                    }
                } else {
                    $serverPortShow = 0;
                    $serverPort     = 25565;
                }
                $sql = "INSERT INTO `mcsz`.`mcsz_servers` (`server_id`, `server_owner`, `server_description`, `server_name`, `server_ip`, `server_port`, `server_showPort`, `server_bannerLocation`, `server_dateAdded`, `server_country`, `server_website`, `server_public_key`, `server_votifier_port`, `server_use_votifier`) VALUES (NULL, '$username', '$serverDesc', '$serverName', '$serverIP', '$serverPort', '$serverPortShow', '$serverBannerLoc', '$serverTime', '$serverCountry', '$serverWebsite', '$serverVotifierPKey', '$serverVotifierPort', '$serverUseVotifier')";
                if (!mysqli_query($con, $sql)) {
                    $error = "Something went wrong while adding your server to our database";
                } else {
                    header("Location: myservers");
                }
            } else {
                $error =  "Sorry, there was an error uploading your file.";
            }
        } else {
            $serverName      = $_POST['serverName'];
            $serverDesc      = $_POST['serverDescription'];
            $serverIP        = $_POST['serverIP'];
            $serverCountry   = $_POST['serverCountry'];
            $serverBannerLoc = "";
            $serverTime      = date("H:i:s d/m/Y");
            $serverWebsite  = $_POST['serverWebsite'];
            
            if ($_POST['useVotifier'] == "on") {
                $serverVotifierPort = $_POST['votifierPort'];
                $serverVotifierPKey = $_POST['votifierPubKey'];
                $serverUseVotifier  = 1;
            } else {
                $serverVotifierPort = "";
                $serverVotifierPKey = "";
                $serverUseVotifier  = 0;
            }
            
            if ($_POST['portCheck'] == "on") {
                $serverPort     = $_POST['portNum'];
                if ($_POST['portNumCheck'] == "on") {
                    $serverPortShow = 1;
                } else {
                    $serverPortShow = 0;
                }
            } else {
                $serverPortShow = 0;
                $serverPort     = 25565;
            }
            $sql = "INSERT INTO `mcsz`.`mcsz_servers` (`server_id`, `server_owner`, `server_description`, `server_name`, `server_ip`, `server_port`, `server_showPort`, `server_bannerLocation`, `server_dateAdded`, `server_country`, `server_website`, `server_public_key`, `server_votifier_port`, `server_use_votifier`) VALUES (NULL, '$username', '$serverDesc', '$serverName', '$serverIP', '$serverPort', '$serverPortShow', '$serverBannerLoc', '$serverTime', '$serverCountry', '$serverWebsite', '$serverVotifierPKey', '$serverVotifierPort', '$serverUseVotifier')";
            if (!mysqli_query($con, $sql)) {
                $error = "Something went wrong while adding your server to our database";
            } else {
                header("Location: myservers");
            }
        }
    }
}

if(isset($error)) {
    $servername             = $_POST['serverName'];
    $serverdescription      = $_POST['serverDescription'];
    $serverip               = $_POST['serverIP'];
    $serverCountry          = $_POST['serverCountry'];
    $serverWebsite          = $_POST['serverWebsite'];
    $serverVotifierPort12   = $_POST['votifierPort'];
    $serverVotifierPKey12   = $_POST['votifierPubKey'];
}


if($con == false){
    $error = "Failed. Please try again later";
} else {
    $rootFolder = dirname(__FILE__);
    ini_set("upload_tmp_dir", $rootFolder."/tempbanners/" );
    ?>
    <html>
    
    <head>
        <title>Add Server | MinecraftServerZone</title>
        <link href="/required/css/bootstrap-switch.css" rel="stylesheet">
        <link href="/required/css/chosen.min.css" rel="stylesheet">
    </head>
    
    <body>
        <?php require 'nav/nav-servers.php'?>
        <script src="/required/js/bootstrap-switch.js"></script>
        <script src="/required/js/chosen.min.js"></script>
        
        <div class="container" style="background-color: white;">
        <?php
        if ($tooMany) {
            ?>
            You have too many servers...<br><br>
            <a href="myservers">Click here to change that</a>
            <?php
        } else {
            echo $left
            ?>
            <form action="" id="addsvrForm" method="POST" enctype="multipart/form-data">
                <h6>Server Name: </h6>
                <input type="text" placeholder="Exmaple: MinecraftServerZone" class="form-control input-sm" length="40" name="serverName"<?php if(isset($error))echo " value=\"$servername\"";?>><br>
                  <h6>Description: </h6>
                <textarea form="addsvrForm" rows="5" placeholder="Minimum of 50 characters, maximum of 1000" class="form-control" name="serverDescription"><?php if(isset($error))echo "$serverdescription";?></textarea><br>
                <h6>Server IP (host): </h6>
                <input type="text" id="serverIP" placeholder="Example IP: 127.0.0.1 ... Example Host: play.mcsz.net" class="form-control input-sm" length="30" id="serverIP" name="serverIP"<?php if(isset($error))echo " value=\"$serverip\"";?>><br>
                
                <label for="portCheck" ><h6>I have a port other than 25565</h6></label><br>
                
               
                <input name="portCheck" class="showPort" id="portBox" type="checkbox"/><br>
                <div id="portContainer" style="margin-left:30px;display:none">
                    <br>
                    <label for="portNum">Your port number</label>
                    <input type="text" id="port" placeholder="Example: 25566" maxlength="6" class="form-control input-sm" name="portNum"/><br>
                    <label for="portNumCheck">Show your port in the server list</label><br>
                    <input type="checkbox" name="portNumCheck"/>
                </div></br>
                <hr>
                <h6>Ping Your Server!</h6><br>
                <btn id="pingTest" class="btn btn-inverse">Ping Now</btn>
                <div id="pingResults"></div>
                <hr>
                <h6>I want to use Votifier</h6>
                <input name="useVotifier" type="checkbox"/><br>
                <div id="votifierContainer" style="margin-left:30px;display:none">
                    <br>
                    <label for="votifierKey">Votifier <i>Public</i> Key</label>
                    <textarea form="addsvrForm" rows="5" id="pubkey" class="form-control" name="votifierPubKey"><?php if(isset($error))echo $serverVotifierPKey12?></textarea><br>
                    <label for="votifierPort">Votifier Port <small class="text-muted">(Default is 8192)</small></label>
                    <input type="text" id="votePort" name="votifierPort" <?php if(isset($error))echo "value=\"".$serverVotifierPort12."\""?>class="form-control"><br>
                    <btn class="btn btn-inverse" id="votifierTest">Test Votifier</btn>
                    <div id="votifierResults"></div>
                </div>
                <hr>
                <!-- I still have to attach this to the database -->
                <br>
                <label><h6>Server Country: </h6></label>
                <div>
                <select form="addsvrForm" name="serverCountry" class="chosen-select select-primary form-control">
                    <option value="US">United States</option><option value="CA">Canada</option><option value="GB">United Kingdom</option><option value="" disabled>--------------------------------</option><option value="AF">Afghanistan</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AQ">Antarctica</option><option value="AG">Antigua and Barbuda</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BS">Bahamas</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia, Plurinational State of</option><option value="BQ">Bonaire, Sint Eustatius and Saba</option><option value="BA">Bosnia and Herzegovina</option><option value="BW">Botswana</option><option value="BV">Bouvet Island</option><option value="BR">Brazil</option><option value="IO">British Indian Ocean Territory</option><option value="BN">Brunei Darussalam</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CV">Cape Verde</option><option value="KY">Cayman Islands</option><option value="CF">Central African Republic</option><option value="TD">Chad</option><option value="CL">Chile</option><option value="CN">China</option><option value="CX">Christmas Island</option><option value="CC">Cocos (Keeling) Islands</option><option value="CO">Colombia</option><option value="KM">Comoros</option><option value="CG">Congo</option><option value="CD">Congo, the Democratic Republic of the</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="HR">Croatia</option><option value="CU">Cuba</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FK">Falkland Islands (Malvinas)</option><option value="FO">Faroe Islands</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="TF">French Southern Territories</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GG">Guernsey</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HM">Heard Island and McDonald Islands</option><option value="VA">Holy See (Vatican City State)</option><option value="HN">Honduras</option><option value="HK">Hong Kong</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IR">Iran, Islamic Republic of</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IM">Isle of Man</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JE">Jersey</option><option value="JO">Jordan</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="KP">Korea, Democratic People's Republic of</option><option value="KR">Korea, Republic of</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Lao People's Democratic Republic</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macao</option><option value="MK">Macedonia, the former Yugoslav Republic of</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islands</option><option value="MQ">Martinique</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="YT">Mayotte</option><option value="MX">Mexico</option><option value="FM">Micronesia, Federated States of</option><option value="MD">Moldova, Republic of</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="ME">Montenegro</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="MM">Myanmar</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="NC">New Caledonia</option><option value="NZ">New Zealand</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="NF">Norfolk Island</option><option value="MP">Northern Mariana Islands</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PS">Palestinian Territory, Occupied</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PN">Pitcairn</option><option value="PL">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RO">Romania</option><option value="RU">Russian Federation</option><option value="RW">Rwanda</option><option value="SH">Saint Helena, Ascension and Tristan da Cunha</option><option value="KN">Saint Kitts and Nevis</option><option value="LC">Saint Lucia</option><option value="MF">Saint Martin (French part)</option><option value="PM">Saint Pierre and Miquelon</option><option value="VC">Saint Vincent and the Grenadines</option><option value="WS">Samoa</option><option value="SM">San Marino</option><option value="ST">Sao Tome and Principe</option><option value="SA">Saudi Arabia</option><option value="SN">Senegal</option><option value="RS">Serbia</option><option value="SC">Seychelles</option><option value="SL">Sierra Leone</option><option value="SG">Singapore</option><option value="SX">Sint Maarten (Dutch part)</option><option value="SK">Slovakia</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="SO">Somalia</option><option value="ZA">South Africa</option><option value="GS">South Georgia and the South Sandwich Islands</option><option value="SS">South Sudan</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="SD">Sudan</option><option value="SR">Suriname</option><option value="SJ">Svalbard and Jan Mayen</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syrian Arab Republic</option><option value="TW">Taiwan, Province of China</option><option value="TJ">Tajikistan</option><option value="TZ">Tanzania, United Republic of</option><option value="TH">Thailand</option><option value="TL">Timor-Leste</option><option value="TG">Togo</option><option value="TK">Tokelau</option><option value="TO">Tonga</option><option value="TT">Trinidad and Tobago</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="TC">Turks and Caicos Islands</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="AE">United Arab Emirates</option><option value="UM">United States Minor Outlying Islands</option><option value="UY">Uruguay</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VE">Venezuela, Bolivarian Republic of</option><option value="VN">Viet Nam</option><option value="VG">Virgin Islands, British</option><option value="VI">Virgin Islands, U.S.</option><option value="WF">Wallis and Futuna</option><option value="EH">Western Sahara</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option>
                </select>
                </div>
                <br><br>
                
                
                
                
                
                
                <label><h6>Server Website: </h6></label>
                <input tye="text" class="form-control input-sm" length="30" name="serverWebsite"<?php if(isset($error))echo " value=\"$serverWebsite\"";?>><br><br>
                <label for="fileToUpload"><h6>Select a banner for your server: </h6>Banner must be a GIF (doesn't have to be animated) and with the dimensions 468x60 pixels</label></br>
                
                <!-- 
                
                
                Make sure we are still able to upload files and that it works correctly 
                Tags other than input make it not work apparently, <button> submits the form so use <btn> instead if you must
                
                
                -->
                <input class="btn btn-default" type="file" name="fileToUpload" id="fileToUpload"></input><br><br>
                <input type="hidden" value="1" name="upload">
                <button class="btn btn-lg btn-primary" type="submit" value="Submit Server" name="submit">Submit</button>
            </form>
        </div>
        
        <script>
        $( document ).ready(function() {
            $("[name='portCheck']").bootstrapSwitch({
                onText: "YES",
                offText: "NO",
                inverse: true,
                onSwitchChange: function() {$("#portContainer").slideToggle(500);}
                });
            $("[name='portNumCheck']").bootstrapSwitch({
                onText: "SHOW",
                offText: "HIDE",
                state: true,
                inverse: true
            });
            $("[name='useVotifier']").bootstrapSwitch({
                onText: "YES",
                offText: "NO",
                inverse: true,
                onSwitchChange: function() {$("#votifierContainer").slideToggle(500);}
            });
            $(".chosen-select").chosen();
        });
        
        $("#pingTest").click(function() {
            var port;
            port = ($("#port").val() >= 1) ? $("#port").val():25565;
            
            var getLink = "server=" + $("#serverIP").val() + "&port=" + port;
            $("#pingResults").html("<br><img src=\"/static/loading.gif\">");
            $.get("/AJAX/serverPing/pingtest.php?" + getLink, function(data){
               
                $("#pingResults").html("<br>"+data);
            });
        });
        $("#votifierTest").click(function() {
            var port;
            port = ($("#votePort").val() != null) ? $("#votePort").val():8192;
            var pubOrig = $("#pubkey").val();
            var pubEncoded = pubOrig.replace(/\+/g , "%2B");
            
            var getParams = "ip=" + $("#serverIP").val() + "&port=" + port + "&key=" + pubEncoded;
            $("#votifierResults").html("<br><img src=\"/static/loading.gif\">");
            $.get("/AJAX/votifier/main.php?" + getParams, function(data){
                $("#votifierResults").html("<br>"+data);
            });
        });
        </script>
    <?php require "footer/footer.php"?></body>
    
    </html>
<?php
}}
?>