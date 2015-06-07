<?php
if (isset($_GET['fgpassUser'])) {
    $getParam = $_GET['fgpassUser'];
} else {
    $getParam = "This is a fake username to always return false";
}

require $_SERVER["DOCUMENT_ROOT"].'/required/dbcon.php';
function genToken() {
    $possibleString = "0123456789ab0cd1f2gh3jk4lm5np6qr7st8v9wx0yz0123456789";
    $token = '';
    for ($i = 1; $i <= 45; $i += 1) {
        $charSelector = mt_rand(0, 52);
        $token .= $possibleString{$charSelector};
    }
    return $token;
}

if (preg_match("/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $getParam)) {
    $email = $getParam;
    $sql = "SELECT members_email FROM `mcsz`.`mcsz_members` WHERE members_email = '$email' LIMIT 1";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) == 1) {
        $token = genToken();
        $userEmail = mysqli_fetch_assoc($result);
        $userEmail = $userEmail['members_email'];
        //Send token to email
        $sql = "UPDATE `mcsz`.`mcsz_members` SET members_pwResetToken = '$token' WHERE members_email = '$userEmail'";
        if (mysqli_query($con, $sql)) {
            echo "A password reset token has been sent to your email address";
        } else {
            echo "Something seems to have gone wrong resetting your password";
        }
    } else {
        echo "That username/email is not in our system";
    }
} else {
    if (preg_match("/^[A-Za-z0-9_]+$/", $getParam)) {
        $username = $getParam;
        $sql = "SELECT members_email FROM `mcsz`.`mcsz_members` WHERE members_username = '$username' LIMIT 1";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) == 1) {
            $token = genToken();
            $userEmail = mysqli_fetch_assoc($result);
            $userEmail = $userEmail['members_email'];
            //Send token to email
            $sql = "UPDATE `mcsz`.`mcsz_members` SET members_pwResetToken = '$token' WHERE members_email = '$userEmail'";
            if (mysqli_query($con, $sql)) {
                echo "A password reset token has been sent to your email address";
            } else {
                echo "Something seems to have gone wrong resetting your password";
            }
        } else {
           echo "That username/email is not in our system";
        }
    } else {
        echo "Please enter a valid username or email";
    }
}