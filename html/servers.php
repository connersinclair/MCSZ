<?php

require 'required/dbcon.php';

if($con == false){
    $error = "No MySQL connection available";
}

if (isset($_GET['emailsuccess'])) {
    $success = "You have successfully registered for an account. You may now login!";
}
?>

<?php require 'nav/nav-servers.php'; ?>
<head>
<title>Servers | MCSZ</title>

<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.zclip.js"></script>
</head>
<body>
	<script>
    $(document).ready(function(){
        /*
        $('a#copy-description').zclip({
            path:'js/ZeroClipboard.swf',
            copy:$('p#description').text()
        });
        // The link with ID "copy-description" will copy
        // the text of the paragraph with ID "description"
        $('a#copy-dynamic').zclip({
            path:'js/ZeroClipboard.swf',
            copy:function(){return $('input#dynamic').val();}
        });
        // The link with ID "copy-dynamic" will copy the current value
        // of a dynamically changing input with the ID "dynamic"
        */
        <?php
        if (isset($_GET['emailsuccess'])) {
        ?>
        window.history.pushState('Servers | MCSZ', 'Servers | MCSZ', '/servers');
        <?php
        }
        ?>
    });
    </script>


<div class="container">

    <br><h2>Sponsored</h2>
    
    <!-- Multiplex -->
    <div class="container">

    <table style="width:100%;border-spacing: 10px;border-collapse: separate;">
        <tr>
            <td style="text-align: center"><b>Ranking</td>
            <td style="text-align: center"><b>Name</td>
            <td style="text-align: center"><b>Banner</td>
            <td style="text-align: center;padding-left:20px"><b>Players</td>
            <td style="text-align: center;padding-left:50px"><b>Vote</td>
        </tr>
        <tr>
            <td style="text-align: center"><h5><span class="label label-default">$</span></h5></td>
            <td style="text-align: center" valign="middle"><h6>Test Sponsor</h6></td>
            <td style="text-align: center">
                <a href="/vote/#"><img src="/static/nobanner.png"></img></a>
                <div class="ctc-parent"><p class="ctc-wrapper-sponsor" id="description"><a class="ctc-link" title="Copy IP to Clipboard" href="#">Sponsor IP address</a></p></div>
            </td>
            <td style="text-align: center;padding-left:20px"><h6><span class="text-danger">Offline</span></h6></td>
            <td style="text-align: center;padding-left:50px"><a href="/vote/#" class="btn btn-primary btn">Vote</a></td>
        </tr>

    </table>
        
    
    
    
    
    
    
    
    
    
    </div>
    
    
    
    <br>
    
    <!--<center><table style="text-align:center;">
      <tr>
        <td>1</td>
        <td>Mineplex</td>
        <td>Banner</td>
        <td>5000/5000</td> 	
        <td>Vote</td>
      </tr>
      <tr>
        <td>Eve</td>
        <td>Jackson</td> 
        <td>94</td>
      </tr>
    </table></center>-->
        





    
    <br><br>
    <ul class="nav nav-tabs nav-justified">
      <li class="active"><a data-toggle="tab" href="#ranked">Top Ranked</a></li>
      <li><a data-toggle="tab" href="#hot">What's Hot</a></li>
      <li><a data-toggle="tab" href="#random">Random</a></li>
    </ul>
    
    
    
    
    <!-- Info Bar At Top -->
    <div class="tab-content">
        
        <!-- 'Random' tab -->
        <div id="random" class="tab-pane fade">
            <h2 style="margin-top:75px;">Random Servers</h2>
            <table style="width:100%;border-spacing: 10px;border-collapse: separate;">
                <tr>
                    <td style="text-align: center"><b>Ranking</td>
                    <td style="text-align: center"><b>Name</td>
                    <td style="text-align: center"><b>Banner</td>
                    <td style="text-align: center;padding-left:20px"><b>Players</td>
                    <td style="text-align: center;padding-left:50px"><b>Vote</td>
                </tr>
            <?php
            $numRandom = 10;
            
            $rankings = file_get_contents("cronScripts/server_rankings/ranking.json");
            $ranks = json_decode($rankings, true);
            end($ranks);
            $lastRank = key($ranks);
            reset($ranks);
            
            $serverStatus = json_decode(file_get_contents("cronScripts/server_ping/rankingpings.json"), true);
            
            $numRandom++;
            $i = 1;
            while ($i < $numRandom) {
                $currRank = mt_rand(1, $lastRank);
                $serverID    = $ranks[$currRank]['0'];
                $serverVotes = $ranks[$currRank]['1'];
                $sql = "SELECT server_bannerLocation, server_name, server_ip, server_port, server_showPort FROM `mcsz`.`mcsz_servers` WHERE server_id = '$serverID'";
                $serverInfo = mysqli_query($con, $sql);
                $serverInfo = mysqli_fetch_assoc($serverInfo);
                
                if (strlen($serverInfo['server_bannerLocation']) < 4) {
                    $serverInfo['server_bannerLocation'] = "/static/nobanner.png";
                } else {
                    $serverInfo['server_bannerLocation'] = "banners/".$serverInfo['server_bannerLocation'];
                    if (!file_exists($serverInfo['server_bannerLocation'])) {
                        $serverInfo['server_bannerLocation'] = "/static/nobanner.png";
                    } else {
                        $serverInfo['server_bannerLocation'] = "/".$serverInfo['server_bannerLocation'];
                    }
                }
                if ($serverInfo['server_showPort'] == 1) {
                    $IP = $serverInfo['server_ip'].":".$serverInfo['server_port'];
                } else {
                    $IP = $serverInfo['server_ip'];
                }
                if ($serverStatus[$serverID]['success'] === "true") {
                    $serverCurrentStatus = $serverStatus[$serverID]['players']."/".$serverStatus[$serverID]['max'];
                } else {
                    $serverCurrentStatus = "<span class=\"text-danger\">Offline</span>";
                }
            ?>
            <tr>
                <td style="text-align: center"><h5><span class="label label-default"><?= $currRank?></span></h5></td>
                <td style="text-align: center" valign="middle"><h6><?= $serverInfo['server_name']?></h6></td>
                <td><div class="ctc-parent"><a href="/vote/<?= $serverID?>"><img src="<?= $serverInfo['server_bannerLocation']?>"></img></a><div><p class="ctc-wrapper-table" id="description"><a class="ctc-link" title="Copy IP to Clipboard" href="#"><?=$IP?></a></p></div></div></td>
                <td style="text-align: center;padding-left:20px"><h6><?= $serverCurrentStatus?></h6></td>
                <td style="text-align: center;padding-left:50px"><a href="/vote/<?= $serverID?>" class="btn btn-primary btn">Vote</a></td>
            </tr>
            <?php
            $i++;
            }
            ?>
            </table>
        </div>
        
        <!-- 'What's Hot' tab -->
        <div id="hot" class="tab-pane fade">
            <div style="text-align: center"><h4>We need more servers with active voters in order to make this feature work. Sorry.</h4></div>
        </div>
        
        <!-- 'Ranked' tab -->
        <div id="ranked" class="tab-pane fade in active">
        <h2 style="margin-top:75px;">Ranked Servers</h2>
        <table style="width:100%;border-spacing: 10px;border-collapse: separate;">
            <tr>
                <td style="text-align: center"><b>Ranking</td>
                <td style="text-align: center"><b>Name</td>
                <td style="text-align: center"><b>Banner</td>
                <td style="text-align: center;padding-left:20px"><b>Players</td>
                <td style="text-align: center;padding-left:50px"><b>Vote</td>
            </tr>
        <?php
        
        //Show x amount of servers
        $numServersPP = 10;
        
        $rankings = file_get_contents("cronScripts/server_rankings/ranking.json");
        $ranks = json_decode($rankings, true);
        end($ranks);
        $lastRank = key($ranks);
        reset($ranks);
        
        $serverStatus = json_decode(file_get_contents("cronScripts/server_ping/rankingpings.json"), true);
        
        $lastPage = ceil($lastRank / $numServersPP);
        
        if (isset($_GET['page'])) {
            if ($_GET['page'] > $lastPage) {
                $curPage = $lastPage;
            } elseif ($_GET['page'] < 1) {
                $curPage = 1;
            } else {
                $curPage = $_GET['page'];
            }
        } else {
            $curPage = 1;
        }
        
        $goForwardInArray = ($curPage * $numServersPP) - $numServersPP;
        $diffLastAndCurrent = $lastRank - $goForwardInArray;
        
        if ($diffLastAndCurrent < $numServersPP) {
            $diffTempVar = $numServersPP - $diffLastAndCurrent;
            $goForwardInArray = $goForwardInArray - $diffTempVar;
        }
        
        $pagination;
        $plusOnePage  = $curPage + 1;
        $plusTwoPages = $curPage + 2;
        $minusOnePg   = $curPage - 1;
        $minusTwoPgs  = $curPage - 2;
        
        $lstMin1 = $lastPage - 1;
        $lstMin2 = $lastPage - 2;
        $lstMin3 = $lastPage - 3;
        $lstMin4 = $lastPage - 4;
        
        if ($curPage == 1) {
            $pagination .= "<li class=\"previous disabled\"><a href=\"#\" class=\"fui-arrow-left\"></a></li>";
            $pagination .= "<li class=\"active\"><a href=\"/servers\">1</a></li>";
            $pagination .= "<li><a href=\"/servers/2\">2</a></li>";
            $pagination .= "<li><a href=\"/servers/3\">3</a></li>";
            $pagination .= "<li><a href=\"/servers/4\">4</a></li>";
            $pagination .= "<li><a href=\"/servers/5\">5</a></li>";
            $pagination .= "<li class=\"next\"><a href=\"/servers/2\" class=\"fui-arrow-right\"></a></li>";
        } elseif ($curPage == 2) {
            $pagination .= "<li class=\"previous\"><a href=\"/servers\" class=\"fui-arrow-left\"></a></li>";
            $pagination .= "<li><a href=\"/servers\">1</a></li>";
            $pagination .= "<li class=\"active\"><a href=\"/servers/2\">2</a></li>";
            $pagination .= "<li><a href=\"/servers/3\">3</a></li>";
            $pagination .= "<li><a href=\"/servers/4\">4</a></li>";
            $pagination .= "<li><a href=\"/servers/5\">5</a></li>";
            $pagination .= "<li class=\"next\"><a href=\"/servers/3\" class=\"fui-arrow-right\"></a></li>";
        /* REMOVE WHEN 5 PAGES OF SERVERS EXISTS
        } elseif ($curPage == $lastPage) {
            $pagination .= "<li class=\"previous\"><a href=\"/servers/$lastMin1\" class=\"fui-arrow-left\"></a></li>";
            $pagination .= "<li><a href=\"/servers/$lstMin4\">$lstMin4</a></li>";
            $pagination .= "<li><a href=\"/servers/$lstMin3\">$lstMin3</a></li>";
            $pagination .= "<li><a href=\"/servers/$lstMin2\">$lstMin2</a></li>";
            $pagination .= "<li><a href=\"/servers/$lstMin1\">$lstMin1</a></li>";
            $pagination .= "<li class=\"active\"><a href=\"/servers/$lastPage\">$lastPage</a></li>";
            $pagination .= $pagination .= "<li class=\"next disabled\"><a href=\"#\" class=\"fui-arrow-right\"></a></li>";
        } elseif ($curPage == $lstMin1) {
            $pagination .= "<li class=\"previous\"><a href=\"/servers/$lastMin2\" class=\"fui-arrow-left\"></a></li>";
            $pagination .= "<li><a href=\"/servers/$lstMin4\">$lstMin4</a></li>";
            $pagination .= "<li><a href=\"/servers/$lstMin3\">$lstMin3</a></li>";
            $pagination .= "<li><a href=\"/servers/$lstMin2\">$lstMin2</a></li>";
            $pagination .= "<li class=\"active\"><a href=\"/servers/$lstMin1\">$lstMin1</a></li>";
            $pagination .= "<li><a href=\"/servers/$lastPage\">$lastPage</a></li>";
            $pagination .= $pagination .= "<li class=\"next\"><a href=\"/servers/$lastPage\" class=\"fui-arrow-right\"></a></li>";
        REMOVE WHEN 5 PAGES OF SERVERS EXISTS */
        } elseif ($curPage > 2) {
            $pagination .= "<li class=\"previous\"><a href=\"/servers/$minusOnePg\" class=\"fui-arrow-left\"></a></li>";
            $pagination .= "<li><a href=\"/servers/$minusTwoPgs\">$minusTwoPgs</a></li>";
            $pagination .= "<li><a href=\"/servers/$minusOnePg\">$minusOnePg</a></li>";
            $pagination .= "<li class=\"active\"><a href=\"/servers/$curPage\">$curPage</a></li>";
            $pagination .= "<li><a href=\"/servers/$plusOnePage\">$plusOnePage</a></li>";
            $pagination .= "<li><a href=\"/servers/$plusTwoPages\">$plusTwoPages</a></li>";
            $pagination .= "<li class=\"next\"><a href=\"/servers/$plusTwoPages\" class=\"fui-arrow-right\"></a></li>";
        }
        
        for ($i = ($goForwardInArray + 1); $i <= ($goForwardInArray + $numServersPP); $i++) {
            $serverID    = $ranks[$i]['0'];
            $serverVotes = $ranks[$i]['1'];
            $sql = "SELECT server_bannerLocation, server_name, server_ip, server_port, server_showPort FROM `mcsz`.`mcsz_servers` WHERE server_id = '$serverID'";
            $serverInfo = mysqli_query($con, $sql);
            $serverInfo = mysqli_fetch_assoc($serverInfo);
            
            if (strlen($serverInfo['server_bannerLocation']) < 4) {
                $serverInfo['server_bannerLocation'] = "/static/nobanner.png";
            } else {
                $serverInfo['server_bannerLocation'] = "banners/".$serverInfo['server_bannerLocation'];
                if (!file_exists($serverInfo['server_bannerLocation'])) {
                    $serverInfo['server_bannerLocation'] = "/static/nobanner.png";
                } else {
                    $serverInfo['server_bannerLocation'] = "/".$serverInfo['server_bannerLocation'];
                }
            }
            if ($serverInfo['server_showPort'] == 1) {
                $IP = $serverInfo['server_ip'].":".$serverInfo['server_port'];
            } else {
                $IP = $serverInfo['server_ip'];
            }
            
            if ($serverStatus[$serverID]['success'] === "true") {
                $serverCurrentStatus = $serverStatus[$serverID]['players']."/".$serverStatus[$serverID]['max'];
            } else {
                $serverCurrentStatus = "<span class=\"text-danger\">Offline</span>";
            }
            ?>
            <tr>
                <td style="text-align: center"><h5><span class="label label-default"><?= $i?></span></h5></td>
                <td style="text-align: center" valign="middle"><h6><?= $serverInfo['server_name']?></h6></td>
                <td><div class="ctc-parent"><a href="/vote/<?= $serverID?>"><img src="<?= $serverInfo['server_bannerLocation']?>"></img></a><div><p class="ctc-wrapper-table" id="description"><a class="ctc-link" title="Copy IP to Clipboard" href="#"><?=$IP?></a></p></div></div></td>
                <td style="text-align: center;padding-left:20px"><h6><?= $serverCurrentStatus?></h6></td>
                <td style="text-align: center;padding-left:50px"><a href="/vote/<?= $serverID?>" class="btn btn-primary btn">Vote</a></td>
            </tr>
            <?php
        }
        ?>
        </table>
        
        <div class="pagination">
            <ul>
                <?= $pagination?>
            </ul>
        </div>
        <div class="container" style="margin-top: 5px;">
            <span class="text-muted pull-right"><i>We currently have <?php $lastRank; echo $lastRank;?> ranked servers!</i></span>
        </div>
        <script>
            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip({
                    delay: { "show": 750, "hide": 100 }
                });
            });
        </script>
    </div>
</div>
</div>
<?php require "footer/footer.php"?>
</body>