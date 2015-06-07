<?php
$token = $_GET['token'];
if (isset($_GET['token']) || $_GET['token'] != null) {
    if (preg_match("/^[a-z0-9]+$/", $token)) {
        require 'required/dbcon.php';
        $sql = "SELECT members_username, members_password, members_email, members_joinDate FROM `mcsz`.`mcsz_members_temp` WHERE members_emailToken = '$token' LIMIT 1";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) == 1) {
            $result = mysqli_fetch_assoc($result);
            $username = $result['members_username'];
            $password = $result['members_password'];
            $email    = $result['members_email'];
            $joinDate = $result['members_joinDate'];
            $id       = "";
            
            $passwords = explode(".", $password);
            $password = $passwords['0'];
            
            $sql = "INSERT INTO `mcsz`.`mcsz_members` (members_id, members_username, members_password, members_email, members_joinDate) VALUES ('$id', '$username', '$password', '$email', '$joinDate')"; 
            
            if (!mysqli_query($con, $sql)) {
                $error = "There was an error activating your account";
            } else {
                $deleteSql = "DELETE FROM `mcsz`.`mcsz_members_temp` WHERE members_emailToken = '$token'";
                mysqli_query($con, $deleteSql);
                session_start();
                $_SESSION['token'] = null;
                $_SESSION['email'] = null;
                session_write_close();
                
                $hashedPass2 = $passwords['1'];
                
                $multiplier = 2.56;
                $multBy = 1;
                while ($multBy != 51) {
                    $char = $multiplier * $multBy;
                    $char = round($char, 0, PHP_ROUND_HALF_UP);
                    $char--;
                    $fullSalt .= $password{$char};
                    $multBy++;
                }
                $saltNorm = $fullSalt;
                $fullSalt = strrev($fullSalt);
                
                //Salt the bitch
                $i = 1;
                $changedPass = $hashedPass2;
                while ($i != 51) {
                    $changedPass = substr_replace($changedPass, $fullSalt{$i - 1}, (round($i*2.56, 0, PHP_ROUND_HALF_UP))+ $i, 0);
                    $i++;
                }
                $userID = "SELECT members_id FROM `mcsz`.`mcsz_members` WHERE members_username = '$username'";
                $userID = mysqli_query($con, $userID);
                $userID = mysqli_fetch_assoc($userID);
                $userID = $userID['members_id'];
                $userArray = $changedPass;

                $newDB = fopen("db/$userID", "w+") or header("Location: /servers/emailsuccess");;
                fwrite($newDB, $changedPass);
				
				$success = true;
                header("Location: /servers/emailsuccess");
            }
        } else {
            header("Location: /servers");
        }
    } else {
        header("Location: /servers");
    }
} else {
    header("Location: /servers");
}
?>

<head>
    <title>Confirm Email | MCSZ</title>
</head>

<body>
    <?php include 'nav/nav-servers.php';?>
    <div class="container" style="margin-top: 20px;">

    </div>
<?php require "footer/footer.php"?></body>