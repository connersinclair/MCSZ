<?php
session_start();
$token = $_SESSION['token'];
$email = $_SESSION['email'];

if (isset($_GET['e'])) $email = $_GET['e'];
?>
<head>
    <title>Successfully Registered | MCSZ</title>
</head>

<body>
    <?php include 'nav/nav-servers.php';?>
    <div class="container" style="margin-top: 20px;">
        <center>
            Thank you for registering with MCSZ. All that is left before you can login is to confirm your account by checking your email and clicking the link from us.<br><br>
            The link has been sent to the following email: <?= $email;?>
        </center>
        
    </div>
<?php require "footer/footer.php"?></body>
