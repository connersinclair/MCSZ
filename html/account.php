<?php
if (isset($_POST['changePass'])) {
    require 'required/dbcon.php';
    $oldPass = $_POST['currentPass'];
    $newPass = $_POST['newPass'];
    $username = $_COOKIE['username'];
    
    $hashedPass = hash('sha512', $oldPass);
            
    $passwordSalt = '';
    for ($i = 1; $i <= 121; $i += 10) {
        $passwordSalt .= $hashedPass{$i};
    }
    $hashedPass = substr($hashedPass, 13);
    $oldPass = $passwordSalt . $hashedPass;
    
    $sql = "SELECT members_password FROM `mcsz`.`mcsz_members` WHERE members_username ='$username'";
    $result = mysqli_query($con, $sql);
    $result = mysqli_fetch_assoc($result);
    $currentPass = $result['members_password'];
    
    if ($currentPass == $oldPass) {
        $hashedPass = hash('sha512', $newPass);
            
        $passwordSalt = '';
        for ($i = 1; $i <= 121; $i += 10) {
            $passwordSalt .= $hashedPass{$i};
        }
        $hashedPass = substr($hashedPass, 13);
        $newPass = $passwordSalt . $hashedPass;
        
        $sql = "UPDATE `mcsz`.`mcsz_members` SET members_password = '$newPass' WHERE members_username = '$username'";
        if (mysqli_query($con, $sql)) {
            $success = "Your password has been changed :)";
        }
    } else {
        $error = "The old password you entered does not match the one in our system";
    }
    unset($oldPass);
    unset($newPass);
    unset($result);
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == "altpass") {
        if (isset($_COOKIE['username'])) {
            header("Location: /logout");
        } else {
            if (!isset($_GET['fmod'])) {
                header("Location: /");
            } else {
                if (!preg_match("/^[a-z0-9]+$/", $_GET['fmod'])) {
                    header("Location: /");
                } else {
                    $strLen = strlen($_GET['fmod']);
                    if ($strLen != 45) {
                        header("Location: /");
                    } else {
                        require 'required/dbcon.php';
                        $rToken = $_GET['fmod'];
                        $sql = "SELECT members_id FROM `mcsz`.`mcsz_members` WHERE members_pwResetToken = '$rToken' LIMIT 1";
                        $usrId = mysqli_query($con, $sql);
                        if (mysqli_num_rows($usrId) == 0) {
                            $altpassInfo = "No password reset token matches the one you gave us";
                        } else {
                            $possibleString = "0123456789ab0cd1fg2hj3kl4mn5pq6rst7vw8xy9z0123456789";
                        
                            $actualPass = "";
                            for ($i = 1; $i <= 15; $i += 1) {
                                $charSelector = mt_rand(0, 51);
                                $actualPass .= $possibleString{$charSelector};
                            }
                            
                            $hashedPass = hash('sha512', $actualPass);
                            
                            $passwordSalt = '';
                            for ($i = 1; $i <= 121; $i += 10) {
                                $passwordSalt .= $hashedPass{$i};
                            }
                            $hashedPass = substr($hashedPass, 13);
                            $newPass = $passwordSalt . $hashedPass;
                            
                            $sql = "UPDATE `mcsz`.`mcsz_members` SET members_password = '$newPass', members_pwResetToken = '' WHERE members_pwResetToken = '$rToken'";
                            mysqli_query($con, $sql);
                            #Send new password in email instructing to go change it
                            
                            $altpassInfo = "Your new password has been sent to the same email address<br>$actualPass (Testing only)";
                        }
                    }
                }
            }
        }
    }
}
?>
<head>
    <title>Account Settings | MCSZ</title>
</head>

<body>
    <?php include 'nav/nav-servers.php'?>
    <div class="container" style="margin-top: 20px;">
        <?php 
        if (isset($_COOKIE['username'])) {
            ?>
            <h2>Change Password</h2>
            <hr>
            <form method="post" onsubmit="return checkForm(this)" action="">
                <label for="currentPass">Current Password</label>
                <input type="password" id="currentPass" class="form-control" name="currentPass"><br>
                <label for="newPass">New Password</label>
                <input type="password" class="form-control" name="newPass"><br>
                <label for="newPassA">Confirm New Password</label>
                <input type="password" class="form-control" name="newPassA"><br>
                <input type="hidden" name="changePass" value="changePass">
                <input type="submit" class="btn btn-primary pull-right" id="changeBtn" value="Change">
            </form>
            <script>
            function checkForm(form) {
                var text1 = form.newPass.value;
                var text2 = form.newPassA.value;
                
                //All these checks are for you, the user
                //You can disable them pretty easily, but we'll still simply reject any incorrectly entered information :)
                //If you do disable them, we take your password field as your final password, so make sure it's right
                //We will not release your username if you disabled our checks due to retardation
                
                if (text1 != text2) {
                    alert("Your passwords do not match");
                    return false;
                } else {
                    return true;
                }
            }
            </script>
            <?php
        } else {
            if(isset($altpassInfo)) { 
                ?>
                <h3 style="text-align: center"><?=$altpassInfo?></h3>
                <?php
            } else {
            ?>
            <h2 style="text-align: center">You are not signed in. Please sign in to view this page</h2>
            <?php
            }
        }
        ?>
    </div>
    </br><br>
    <?php require "footer/footer.php"?></body>