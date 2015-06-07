<?php

require 'required/dbcon.php';

if(isset($_POST['username'])) {
    //Start IF tree with many checks for registration...yay
    //Add alert for anything wrongly entered
    
    //All the POST'd values here as variables
    $username = $_POST['username'];
    $password = $_POST['password'];
    $botCheck = $_POST['botCheck'];
    $email    = $_POST['email'];
    
    $sql = "SELECT `members_username` FROM `mcsz`.`mcsz_members` WHERE `members_username` = '$username';";
    $usernameCheck = mysqli_query($con, $sql);
    
    $sql = "SELECT `members_email` FROM `mcsz`.`mcsz_members` WHERE `members_email` = '$email';";
    $emailCheck = mysqli_query($con, $sql);
    //Client will not see register page again if all goes according to our requirements
    if (!preg_match("/^[A-Za-z0-9_]+$/", $username)) {
            $error = "Your username must only contain Alpha-Numerical characters, or an underscore";
    } elseif (strlen($username) <= 5) {
            $error = "Your username must be longer than 5 characters long.";
    } elseif (strlen($username) >= 16) {
            $error = "Your username cannot be longer than 16 characters.";
    } elseif (strlen($password) <= 5) {
        $error = "Your password must be longer than 5 characters long.";
    } elseif (strlen($email) <= 5) {
            $error = "Please enter a valid email address.";
    } elseif (!preg_match("/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $email)) {
            $error = "Please enter a valid email address.";
    } elseif (mysqli_num_rows($usernameCheck) >= 1) {
            $error = "That username is already being used.";
    } elseif (mysqli_num_rows($emailCheck) >= 1) {
            $error = "That email is already being used.";
    } elseif ($botCheck != $_SESSION['registerRand']) {
            $error = "The code you entered does not match the one given to you. Please try again.";
    } else {
        //Do email stuff yay
        //Add email confirm code to a temporary database with all needed info
        //When that link is clicked on, move the entry to members table
        //Figure out the rest at a later time...
        
        //Generate our "safe" passwords
        $hashedPass = hash('sha512', $password);
        
        $passwordSalt = '';
        for ($i = 1; $i <= 121; $i += 10) {
            $passwordSalt .= $hashedPass{$i};
        }
        $hashedPass = substr($hashedPass, 13);
        $passwordFinal = $passwordSalt . $hashedPass;
        
        //Set variables and shit
        $id       = "";
        $username = $username;
        $password = $passwordFinal;
        $email    = $email;
        $joinDate = date("H:i:s d-m-Y");
        $token = "";
        $expiry = "";
        
        $sql = "INSERT INTO `mcsz`.`mcsz_members` (members_id, members_username, members_password, members_email, members_joinDate, members_sessionToken, members_sessionExpiry) VALUES ('$id', '$username', '$password', '$email', '$joinDate', '$token', '$expiry')"; 
        mysqli_query($con, $sql);
        
        unset($hashedPass);
        unset($username);
        unset($password);
        unset($email);
        
        //Send to page with thing to check email
        //header("Location: /emailConfirm.php?token=RANDOM STUFF");
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Register | MinecraftServerZone</title>
    </head>
    <body>
    
<?php
if($con == false){
    $error = "MySQL server connection failed. Please try again later. We will not allow login attempts during this time.";
} else {
    ?>
    <form action".." onsubmit="return checkForm(this)" method="post">
        <!-- You can change the regex, max length and everything...see if we care on the backend -->
        <label for="username">Username</label>
        <input type="textbox" name="username" <?php if(isset($username))echo "value=\"$username\" " ?>maxlength="16" autofocus required/><br>
        <label for="password">Password</label>
        <input type="password" name="password" maxlength="256" required/><br>
        <label for="passconf">Confirm Password</label>
        <input type="password" name="passconf" maxlength="256" required/><br>
        <label for="email">Your Email</label>
        <input type="email" name="email" <?php if(isset($email))echo "value=\"$email\" " ?>required/><br><br>
        
        <!-- Botcheck start -->
        <!-- Botcheck start -->
        <!-- Botcheck start -->
        
        <?php
        //Generate random 8 digit number to check for bot, will be made better if we get bombarded with bots >.>
        $_SESSION['registerRand'] = rand(10000000,99999999);
        echo "<label for=\"auth\">Type the following number into the box: ".$_SESSION['registerRand']."</label><br>";
        ?>
        <input type="textbox" name="botCheck" maxlength="8" pattern="([0-9])+" required/><br><br>
        
        <!-- Botcheck end -->
        <!-- Botcheck end -->
        <!-- Botcheck end -->
        
        <input type="checkbox" name="terms" value="agreed">I agree to terms of use of MinecraftServerZone</input><br>
        <input type="submit" value="Register"/>
    </form>
    <?php
}
?>

<?php require "footer/footer.php"?>\n</body>

<script>
function checkForm(form) {
    
    var text1 = form.password.value;
    var text2 = form.passconf.value;
    
    //All these checks are for you, the user
    //You can disable them pretty easily, but we'll still simply reject any incorrectly entered information :)
    //If you do disable them, we take your password field as your final password, so make sure it's right
    //We will not release your username if you disabled out checks due to retardation
    
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
</script>

</html>