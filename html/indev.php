<head>
    
    <!-- Loading Bootstrap -->
    <link href="/dist/css/vendor/bootstrap.min.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="/dist/css/flat-ui.min.css" rel="stylesheet">
    
    <!-- Loading Flat UI .js -->
    <script src="/dist/js/vendor/jquery.min.js"></script>
    <script src="/dist/js/flat-ui.min.js"></script>
    
    
</head>
<body style="background-color:#34495E;">
    <div class="container">
        <a href="/servers"><img style="width:400px;height:height:100px;margin-top:20px;display:block;" src="static/LogoFLAT.png"></img></a>
       <hr>
    </div>
    <div class="container">
        <center>
            <h3 style="color:#fff; margin-top:50px;">This page/service is in development!</br><small>Just a little more time until it's finished!</small></h3></br>
            <div class="progress" style="width:75%;">
                <div class="progress-bar progress-bar-striped active" style="width:83%;"></div>
            </div></br>
            <a href="/servers" class="btn btn-primary btn-lg">Return to Homepage</a>
        </center>
    </div>
    </br></br></br>
    <?php require "footer/footer.php"?></body>
    <script>
    $(function() {
        $("#container").css("positon", "relative");
        $("#footer").css("bottom", 0);
        $("#footer").css("left", 0);
        $("#footer").css("right", 0);
        $("#footer").css("position", "fixed"); 
    });
    </script>