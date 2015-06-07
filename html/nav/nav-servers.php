<?php
require 'required/dbcon.php';

if ($_SERVER['HTTP_HOST'] == "mcsz.net") {
    header("Location: //minecraftserverzone.net".$_SERVER['REQUEST_URI']);
}

//error_reporting(E_ALL);

//Current page -- __FILE__ will NOT work due to the way we load pages
$uri = $_SERVER["REQUEST_URI"];
$segments = explode('?', $uri, 2); //Take off any get variables
$currentFile = $segments[0];
$currentFile = substr($currentFile, 1); //URI produces a / at the beginning, so here we remove it

function logout() {
    unset($_COOKIE['username']);
    setcookie('username', '', time() - 3600);
    unset($_COOKIE['check']);
    setcookie('check', '', time() - 3600);
    header("Location: /redir.php?ref=$currentFile");
}

//If logout was pressed
if ($_GET['logout']) {
    logout(); 
}

//Check if logged in cookie is untouched
$stayLoggedIn = 1;
if (isset($_COOKIE['username'])) {
    if(isset($_COOKIE['check'])) {
        //Generate username check
        $hashedUser = hash('sha512', $_COOKIE['username']);
        
        $usernameSalt = '';
        for ($i = 1; $i <= 121; $i += 5) {
            $usernameSalt .= $hashedUser{$i};
        }
        $usernameCheck = $usernameSalt;
        
        if($_COOKIE['check'] != $usernameCheck) {
            logout();
        }
    } else {
        logout();
    }
}

if ($_POST['register'] == "register") {
    if(isset($_POST['username'])) {
        
        //Start IF tree with many checks for registration...yay
        //Add alert for anything wrongly entered
        
        //All the POST'd values here as variables
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email    = $_POST['email'];
        
        $sql = "SELECT `members_username` FROM `mcsz`.`mcsz_members` WHERE `members_username` = '$username'";
        $usernameCheck = mysqli_query($con, $sql);
        
        $sql = "SELECT `members_email` FROM `mcsz`.`mcsz_members` WHERE `members_email` = '$email';";
        $emailCheck = mysqli_query($con, $sql);
        
        $sql = "SELECT `members_username` FROM `mcsz`.`mcsz_members_temp` WHERE `members_username` = '$username'";
        $usernameCheck2 = mysqli_query($con, $sql);
        
        $sql = "SELECT `members_email` FROM `mcsz`.`mcsz_members_temp` WHERE `members_email` = '$email';";
        $emailCheck2 = mysqli_query($con, $sql);
        
        $captcha = $_POST['g-recaptcha-response'];
        $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfRNQQTAAAAAEnCf4rJ0vIhpOm_knsUn9htdnwT&response=".$captcha."&remoteip".$_SERVER['REMOTE_ADDR']), true);

	if (preg_match("/^[A-Za-z0-9_]+$/", $username)) {
            if (preg_match("/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $email)) {
                if (strlen($username) <= 5) {
                    $error = "Your username must be longer than 5 characters";
                } elseif (strlen($username) > 16) {
                    $error = "Your username cannot be longer than 16 characters";
                } elseif (strlen($password) < 5) {
                    $error = "Your password must be longer than 5 characters";
                } elseif (strlen($email) <= 5) {
                    $error = "Please enter a valid email address";
                } elseif (mysqli_num_rows($usernameCheck) >= 1) {
                    $error = "That username is already being used";
                } elseif (mysqli_num_rows($emailCheck) >= 1) {
                    $error = "That email is already being used";
                } elseif (mysqli_num_rows($usernameCheck2) >= 1) {
                    $error = "That username is already being used";
                } elseif (mysqli_num_rows($emailCheck2) >= 1) {
                    $error = "That email is already being used";
                } elseif($response['success'] != 1) {
                    if ($response['error-codes'] == "missing-input-response") {
                        $error = "You forgot to click the reCAPTCHA button";
                    } elseif ($response['error-codes'] == "invalid-input-response") {
                        $error = "It seems you may have edited the reCAPTCHA data...don't do that";
                    } else {
                        $error = "An unknown error has occured with your reCATPCHA";
		    		}
                } else {
                    //Generate our "safe" passwords
                    $hashedPass = hash('sha512', $password);
                    $hashedOrig = $hashedPass;
                    
                    $passwordSalt = '';
                    for ($i = 1; $i <= 121; $i += 10) {
                        $passwordSalt .= $hashedPass{$i};
                    }
                    $hashedPass = substr($hashedPass, 13);
                    $passwordFinal = $passwordSalt . $hashedPass;
                    
                    //Generate our email tokens
                    $possibleString = "0123456789abcdfghjklmnpqrstvwxyz0123456789";
                    
                    $token = '';
                    for ($i = 1; $i <= 64; $i += 1) {
                        $charSelector = mt_rand(0, 41);
                        $token .= $possibleString{$charSelector};
                    }
                    
                    
                    //Set variables and shit
                    $id       = "";
                    $username = $username;
                    $password = $passwordFinal.".".$hashedOrig;
                    $email    = $email;
                    $joinDate = date("H:i:s d/m/Y");
                    $emailToken = $token;
                    
                    $sql = "INSERT INTO `mcsz`.`mcsz_members_temp` (members_id, members_username, members_password, members_email, members_joinDate, members_emailToken) VALUES ('$id', '$username', '$password', '$email', '$joinDate', '$emailToken')"; 
                    
					if (mysqli_query($con, $sql)) {
                        try {
                            require_once('mail/mandrill/Mandrill.php');
                            //TEST API
                            //$mandrill = new Mandrill('WTZLCAY_NBfIyf06v74PmA');

                            //Real API
                            $mandrill = new Mandrill('a-lm2bmhVJc697eIqNxNIQ');
                            $message = array(
                                "headers" => array('Reply-To' => 'support@mcsz.net'),
                                "from_email" => "no-reply@mcsz.net",
                                "from_name" => "Minecraft Server Zone",
                                "to"=> array(array('email'=>$email, 'name' => $username, 'type' => 'to')),
                                "subject" =>"MCSZ Account Verification",
                                "tags" => array("account-verification"),
                                "html" => '<style>.heading{width:100%;height:50px}.container{font-family:Lato,Helvetica,Arial,sans-serif;background-color:#34495E;color:#fff}.progress-bar{line-height:12px;background:#1abc9c;box-shadow:none;float:left;height:100%;font-size:12px;color:#fff;text-align:center;border-radius:32px}.progress{height:12px;background:#ebedef;border-radius:32px;box-shadow:none}hr{margin-top:60px;margin-bottom:20px;border:0;border-top:1px solid #eee;height:0;box-sizing:content-box}h2{color:#fff}.btn{padding:10px 15px;font-size:15px;font-weight:400;line-height:1.4;border:none;border-radius:4px;-webkit-transition:border .25s linear,color .25s linear,background-color .25s linear;transition:border .25s linear,color .25s linear,background-color .25s linear;-webkit-font-smoothing:subpixel-antialiased;isplay:inline-block;padding:6px 12px;margin-bottom:0;font-size:14px;font-weight:400;line-height:1.42857143;text-align:center;white-space:nowrap;vertical-align:middle;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background-image:none;border:1px solid transparent;border-radius:4px}.btn-primary{color:#fff;background-color:#1abc9c}.btn-lg{padding:10px 19px;font-size:17px;line-height:1.471;border-radius:6px}.btn:hover{background-color:#16a085}a{text-decoration:none}.text-muted{opacity:.5;font-size:12px;text-align:justify}.unable-click{font-size:13}</style><div class=container><div class=heading><img src=http://mcsz.net/static/LogoFLAT.png alt="MCSZ Logo" height=100></div><hr><center><h2>Just one more step until you can submit a server!</h2><div class=progress style=width:75%><div class="progress-bar progress-bar-striped active" style=width:83%></div></div><br><br><a href="http://mcsz.net/confirmemail?token='.$emailToken.'" class="btn btn-lg btn-primary">Activate your account</a><br><br><br><p class=unable-click>If you are unable to click the link above, copy the following URL and put it into your browser:<br>http://mcsz.net/confirmemail?token='.$emailToken.'</p><hr></center><p class=text-muted>This email was sent by Minecraft Server Zone as the result of a registration process started by a user. If you are not the user, disregard this email. If you do not activate the account, this is the last email you will be getting from us. Otherwise, the only emails that will come from us is ones regarding the integrity of your password and account (data breach).</p></div>',
								"text" => "Activate your account by going to the following link in your browser:\nhttp://mcsz.net/confirmemail?token=".$token."\n\n\nThis email was sent by Minecraft Server Zone as the result of a \nregistration process started by a user. If you are not the user, disregard \nthis email. If you do not activate the account, this is the last email you \nwill be getting from us. Otherwise, the only emails that will come from us\n is ones regarding the integrity of your password and account (data breach)."
							);
                            
                            $result = $mandrill->messages->send($message);
                            echo $result;
							header("Location: /registered?e=".$email);
                        } catch(Mandrill_Error $e) {
                            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
                            throw $e;
                        }
                    } else {
                        $error = "There was a problem adding your account to our database";
                    }
                    
                    unset($emailToken);
                    unset($hashedPass);
                    unset($username);
                    unset($password);
                    unset($token);
                    unset($email);
					
					exit;
                }
            } else {
                $error = "Please enter a valid email address.";
            }
        } else {
            $error = "Your username must only contain Alpha-Numerical characters, or an underscore";
        }
    }
} elseif ($_POST['login'] == "login") {
    if(isset($_POST['email'])) {
        $email    = $_POST['email'];
        $password = $_POST['password'];
        if (!preg_match("/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $email)) {
            $username = $email;
            if (preg_match("/^[A-Za-z0-9_]+$/", $username)) {
                $hashedPass = hash('sha512', $password);
                
                $passwordSalt = '';
                for ($i = 1; $i <= 121; $i += 10) {
                    $passwordSalt .= $hashedPass{$i};
                }
                $hashedPass = substr($hashedPass, 13);
                $passwordFinal = $passwordSalt . $hashedPass;
                
                $sql = "SELECT members_username, members_password FROM `mcsz`.`mcsz_members` WHERE `members_username` = '$username' AND `members_password` = '$passwordFinal'";
                $loginCheckUser = mysqli_query($con, $sql);
                $numRowsUser = mysqli_num_rows($loginCheckUser);
                
                if ($numRowsUser != 1) {
                    $error = "That username/password combination is not correct.";
                } else {
                    //Generate random token string
                    $characters = 'abcdfghjklmnpqrstvwxyz0123456789';
                    $string = '';
                    for ($i = 0; $i <= 128; $i++) {
                        $string .= $characters[rand(0, strlen($characters) - 1)];
                    }
                    $sql = "SELECT members_username FROM `mcsz`.`mcsz_members` WHERE `members_username` = '$username'";
                    $result = mysqli_query($con, $sql);
                    $row = mysqli_fetch_array($result);
                    $username = $row[0];
                    
                    //Generate username check
                    $hashedUser = hash('sha512', $username);
                
                    $usernameSalt = '';
                    for ($i = 1; $i <= 121; $i += 5) {
                        $usernameSalt .= $hashedUser{$i};
                    }
                    $usernameCheck = $usernameSalt;
                    
                    //Remember Me expiry
                    if (isset($_POST['remember'])) {
                        //Remember Me checkbox will have a 14 day expiry
                        $timeNow = time();
                        $sesExpiry = $timeNow + (14 * 86400);
                    } else {
                        //Expire the session 1 day after signing in
                        $timeNow = time();
                        $sesExpiry = $timeNow + 86400;
                    }
                    
                    $cookieName = "username";
                    setcookie($cookieName, $username, $sesExpiry, "/");
                    $cookieName = "check";
                    setcookie($cookieName, $usernameCheck, $sesExpiry, "/");
                    
                    header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                }
            } else {
                $error = "That email/username is invalid";
            }
        } else {
            $hashedPass = hash('sha512', $password);
            
            $passwordSalt = '';
            for ($i = 1; $i <= 121; $i += 10) {
                $passwordSalt .= $hashedPass{$i};
            }
            $hashedPass = substr($hashedPass, 13);
            $passwordFinal = $passwordSalt . $hashedPass;
            
            $sql = "SELECT members_email, members_password FROM `mcsz`.`mcsz_members` WHERE `members_email` = '$email' AND `members_password` = '$passwordFinal'";
            $loginCheckUser = mysqli_query($con, $sql);
            $numRowsUser = mysqli_num_rows($loginCheckUser);
            
            if ($numRowsUser != 1) {
                $error = "That username/password combination is not correct.";
            } else {
                //Generate random token string
                $characters = 'abcdfghjklmnpqrstvwxyz0123456789';
                $string = '';
                for ($i = 0; $i <= 128; $i++) {
                    $string .= $characters[rand(0, strlen($characters) - 1)];
                }
                //Set username
                $sql = "SELECT members_username FROM `mcsz`.`mcsz_members` WHERE `members_email` = '$email'";
                $result = mysqli_query($con, $sql);
                $row = mysqli_fetch_array($result);
                $username = $row[0];
                
                //Generate username check
                $hashedUser = hash('sha512', $username);
                
                $usernameSalt = '';
                for ($i = 1; $i <= 121; $i += 5) {
                    $usernameSalt .= $hashedUser{$i};
                }
                $usernameCheck = $usernameSalt;
                
                //Remember Me expiry
                if (isset($_POST['remember'])) {
                    //Remember Me checkbox will have a 14 day expiry
                    $timeNow = time();
                    $sesExpiry = $timeNow + (14 * 86400);
                } else {
                    //Expire the session 1 day after signing in
                    $timeNow = time();
                    $sesExpiry = $timeNow + 86400;
                }
                
                $cookieName = "username";
                setcookie($cookieName, $username, $sesExpiry, "/");
                $cookieName = "check";
                setcookie($cookieName, $usernameCheck, $sesExpiry, "/");
                
                
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            }
        }
    }
}
?>

 <?php
    if (isset($_COOKIE['username'])) {
        //When logged in
        ?>
        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Alerts</h4>
                    </div>
                    <div class="modal-body">
                        <span style="text-align: center"><h4>You have no new alerts</h4></span>
                    </div>
                    <div class="modal-footer">
                        <btn id="alertClose" data-dismiss="modal" class="btn btn-primary">Close</btn>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
    if($con == false){
        $error = "Authentication server down. We will not allow login attempts during this time.";
    } else {
        //When logged out
        ?>
        <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Register</h4>
                    </div>
                    <div class="modal-body">
                        <form action="" onsubmit="return checkForm(this)" method="post">
                            <!-- You can change the regex, max length and everything...see if we care on the backend -->
                            <label for="username">Username*</label>
                            <input type="text" class="form-control input-sm" name="username" <?php if(isset($username))echo "value=\"$username\" " ?>maxlength="16" autofocus autocomplete="off" required/><br>
                            <label for="password">Password*</label>
                            <input type="password" class="form-control input-sm" name="password" maxlength="256" autocomplete="off" required/><br>
                            <label for="passconf">Confirm Password*</label>
                            <input type="password" class="form-control input-sm" name="passconf" maxlength="256" autocomplete="off" required/><br>
                            <label for="email">Your Email*</label>
                            <input type="email" class="form-control input-sm" name="email" <?php if(isset($email))echo "value=\"$email\" " ?>autocomplete="off" required/><br>
                            <hr><div class="g-recaptcha" data-sitekey="6LfRNQQTAAAAAIBh_Si7fNwrDJ9D7gjvUTRj_5f4"></div><hr>
                            <input type="hidden" name="register" value="register"/>
                            <label class="checkbox" for="checkbox1">
                                <input type="checkbox" value id="checkbox1" name="terms" data-toggle="checkbox" class="custom-checkbox">
                                <span class="icons">
                                    <span class="icon-unchecked"></span>
                                    <span class="icon-checked"></span>
                                </span>
                                I agree to terms of use of MinecraftServerZone
                            </label>
							<p><small>You can read the <a href="/terms" target="_blank">terms here</a></small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
         <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Login</h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        if (isset($loginMessage)) {
                            ?>
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <p style="text-align: center"><?= $loginMessage?></p>
                            </div>
                            <?php
                        }?>
                        <p>
                            <form action="" method="post">
                                <label for="username">Email/Username</label>
                                <input class="form-control input-sm" type="text" name="email" autofocus required/><br>
                                <label for="password">Password</label>
                                <input class="form-control input-sm" type="password" name="password" maxlength="256" required/><br>
                                <input class="form-control input-sm" type="hidden" name="login" value="login"/>
                                <label class="checkbox" for="remember">
                                    <input type="checkbox" value id="remember" name="remember" data-toggle="checkbox" class="custom-checkbox">
                                    <span class="icons">
                                        <span class="icon-unchecked"></span>
                                        <span class="icon-checked"></span>
                                    </span>
                                    Remember Me!
                                </label>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <a data-toggle="modal" data-dismiss="modal" data-target="#forgotPassModal" href="#" class="text-muted pull-left">Can't sign in?</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="forgotPassModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Recover lost or forgotten password</h4>
                    </div>
                    <div class="modal-body">
                        <div id="fgpassContainer">
                            <div id="fgForm">
                                <label for="userfp">Enter your username or email:</label>
                                <input type="text" id="fgpass" name="userfp" class="form-control">
                                <input type="hidden" name="fgpass" value="userfp">
                            </div>
                            <div style="margin-top: 7px; text-align: center" id="fgpassResults"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a data-toggle="modal" data-dismiss="modal" data-target="#loginModal" href="#" class="text-muted pull-left">Back</a>
                        <btn id="fgPassBtn" class="btn btn-primary">Recover Password</btn>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        }
    }
    ?>
    <!-- Top space starts here -->
    <nav>
    <div class="top-space">
        <div style="float:left;">
            <a href="/servers"><img style="height:48px;position:absolute;padding-left:10px;" src="/static/LogoFLAT.png"></img></a>
        </div>
        <nav class="navbar-inverse">
              <div class="navbar-form navbar-right" action="#" role="search" style="margin-top:-3px;margin-right:1px;">
                <div class="form-group">
                  <div class="input-group" id="searchInput">
                    <input class="form-control" id="navbarInput" type="search" placeholder="Search">
                    <span class="input-group-btn">
                      <button id="searchSubmit" class="btn"><span class="fui-search"></span></button>
                    </span>
                  </div>
                </div>
              </div>
		</nav>
    </div>
    </nav>
    <!-- Top space ends here -->
    
    <!-- Nav hold starts here -->
    <div class="n-hold">
        <div class="container">
            <nav class="navbar navbar-inverse">
              <div class="container-fluid">
                 <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span> 
                </button>
              </div>  
              <div class="collapse navbar-collapse" id="myNavbar">      
                  <ul class="nav navbar-nav">
                    <?php
                    //Active page
                    if ($currentFile == "servers") {
                        $serverActive = "1";
                    }
                    
                    /* Sample code for later
                    if ($currentFile = "devs") {
                        $devActive = true;
                    }
                    if ($currentFile = "bt") {
                        $btActive = true;
                    }*/
                    ?>
                    <li<?php if($serverActive == "1"){echo " class=\"active\"";}?>><a href="/">Servers <span class="sr-only">(current)</span></a></li>
                    <li><a href="/indev">Build Teams</a></li>
                  </ul>
                  <ul class="nav navbar-nav n-right">
                    <?php
                        if (isset($_COOKIE['username'])) {
                            $username = $_COOKIE['username'];
                            ?>
                            <!-- Logout and shit dropdown here -->
                            <ul class="nav navbar-nav n-right">
								<li class="navwb"><b>Welcome back, <?=$username?></b></li>
                                <li>
                                    <a data-toggle="modal" data-target="#alertModal" href="#">
                                        <span style="font-size:20px;" class="fui-mail"></span>
                                    </a>
                                </li>
                                <li class="dropdown">
                                  <a class="dropdown-toggle" data-toggle="dropdown" href="#" style="height:100%;"><span style="font-size:20px;" class="fui-gear"></span></a>
                                      <ul class="dropdown-menu" role="menu">
                                        <li><a href="/myservers">My Resources</a></li>
                                        <li><a href="/addresources">Add Resources</a></li>
                                        <li><a href="/auction">Promote Resources</a></li>
                                        <li class="divider"></li>
                                        <li><a href="/account">Account  Settings</a></li>
                                        <li class="divider"></li>
                                        <li><a href="/logout">Logout</a></li>
                                      </ul>
                                </li>
                            </ul>
                            
                            <?php
                            
                        } else {
                            ?>
                            <!--data-toggle="modal" data-target="#registerModal" href="#"
                            a data-toggle="modal" data-target="#loginModal" href="#-->"
                            <ul class="nav navbar-nav n-right">
                                <li><a data-toggle="modal" data-target="#registerModal" href="#">Sign Up</a></li>
                                <li><a data-toggle="modal" data-target="#loginModal" href="#">Login</a></li>
                            </ul>
                            <?php
                        }
                        ?>
                  </ul>
                </div>  
            </nav>
        </div>
    </div>
  </div>
<html>

<head>
	<meta charset="utf-8">
    
    <meta name="viewport" content="width=1000, initial-scale=1.0, maximum-scale=1.0">
    
    <!-- Loading Bootstrap -->
    <link href="/dist/css/vendor/bootstrap.min.css" rel="stylesheet">
    
    <!-- Loading Flat UI -->
    <link href="/dist/css/flat-ui.min.css" rel="stylesheet">
	
	<!-- Force MCSZ css to load last and override if needed -->
    <link href="/dist/mcsz.css" rel="stylesheet">
    
    
    
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
      <script src="/dist/js/vendor/html5shiv.js"></script>
      <script src="/dist/js/vendor/respond.min.js"></script>
    <![endif]-->
    
    <!-- Loading Flat UI .js -->
    <script src="/dist/js/vendor/jquery.min.js"></script>
    <script src="/dist/js/flat-ui.min.js"></script>
    
    <script src='https://www.google.com/recaptcha/api.js'></script>
    
    <script type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({
            placement : 'bottom'
        });
    });
    $("#searchSubmit").click(function() {
       window.location.href = "/search/" + $("#navbarInput").val();  
    });

    //This will accept the enter key for the search bar only if it has focus
    $(document).keypress(function(e) {
        if(e.which == 13) {
            var searchFocus = $("#navbarInput").is(":focus");
            if (searchFocus == true) {
                window.location.href = "/search/" + $("#navbarInput").val();
            }
        }
    });

    /* START --Search focus hack-- START*/
    $("#navbarInput").focusout(function() {
        $("#searchInput").toggleClass("focus");
    });
    $("#navbarInput").focusin(function() {
        $("#searchInput").toggleClass("focus");
    });
    /*  END  --Search focus hack--  END */
    </script>
    
</head>    

<body>
    
    <div class="container">
        <?php
        if (isset($error)) {
            ?>
            <div style="margin-top: 20px;" class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <center><?= $error ?></center>
            </div>
            <?php
        }
        if (isset($success)) {
            ?>
            <div style="margin-top: 20px;" class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <center><?= $success ?></center>
            </div>
            <?php
        }
        ?>
        
        <div style="margin-top: 20px;text-align:center;" class="alert alert-warning" role="alert">
            Since we have launched in the past few weeks, PLEASE contact us with ANY problem that arises<br>
            <a data-toggle="modal" data-target="#support" href="#">Click here to launch our contact module</a>
        </div>
            
        </div>
    
<?php
//Open the login modal if sent from addserver.php due to not being logged in
if($_GET['ref'] == "login") {
    ?>
    <script>
        $( document ).ready(function() {
            $('#loginModal').modal('toggle');
        });
    </script>
    <?php
}
?>
<?php
//Open the login modal if sent from addserver.php due to not being logged in
if($_GET['ref'] == "register") {
    ?>
    <script>
        $( document ).ready(function() {
            $('#registerModal').modal('toggle');
        });
    </script>
    <?php
}
?>
    <?php
    if (!isset($_COOKIE['username'])) {
        ?>
        <script>
            function checkForm(form) {
                
                var text1 = form.password.value;
                var text2 = form.passconf.value;
                
                //All these checks are for you, the user
                //You can disable them pretty easily, but we'll still simply reject any incorrectly entered information :)
                //If you do disable them, we take your password field as your final password, so make sure it's right
                //We will not release your username if you disabled our checks due to retardation
                
                if (form.terms.checked == false) {
                    alert("Please check the box agreeing to our terms.");
                    return false;
                 } else {
                    if (text1 != text2) {
                        alert("Your passwords do not match");
                        return false;
                    } else {
                        return true;
                    }
                }
            }
            var fgFaded = 0;
            var fgFormHTML;
            var fgFormValue;
            var dataBeingSent;
            
            $("#fgPassBtn").click(function() {
                fgFormHTML = $("#fgForm").html();
                fgFormValue = $("#fgpass").val();
                $("#fgpassResults").html("<span><img src=\"/static/loading.gif\"></span>");
                $("#fgPassBtn").addClass("disabled").attr("id", "fgPassDataSending");
                $.get("/AJAX/forgetpass.php?fgpassUser=" + fgFormValue, function(data) {
                    $("#fgpassResults").html(data);
                    $("#fgPassDataSending").removeClass("disabled").attr("id", "fgPassBtn");
                });
            });
        </script>
        <?php
    }
    ?>
    </body>