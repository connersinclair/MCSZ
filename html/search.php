<?php
    require 'required/dbcon.php';

    if (isset($_GET['q'])) {
        $query = $_GET['q'];
		if (preg_match("/^[A-Za-z0-9\:\-\s\.]+$/", $query)) {
            $sql = "SELECT server_name, server_owner, server_bannerLocation FROM `mcsz`.`mcsz_servers` WHERE server_name LIKE '%$query%' LIMIT 10";
            $searchResult = mysqli_query($con, $sql);
            
            if (mysqli_num_rows($searchResult) != 0) {
                $isFound = 1;
            } else {
                $isFound = 0;
            }
        } else {
            $isFound = 0;
        }
    } else {
        header("Location: /");
    }
?>

<?php require 'nav/nav-servers.php'?>  


<head>
    <title>Search</title>
</head>

<body>
    <div class="container">
        <?php
        if ($isFound == 1) {
            foreach ($searchResult as $server) {
        ?>
        <table>
        <tr>
                <td style="text-align: center"><h5><span class="label label-default"><?= $currRank?></span></h5></td>
                <td style="text-align: center" valign="middle"><h6><?= $server['server_name']?></h6></td>
                <td style="text-align: center"><a href="/vote/<?= $serverID?>"><img src="<?= $server['server_bannerLocation']?>"></img></a></td>
                <td style="text-align: center;padding-left:20px"><h6><?= $serverCurrentStatus?></h6></td>
                <td style="text-align: center;padding-left:50px"><a href="/vote/<?= $serverID?>" class="btn btn-primary btn">Vote</a></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td style="text-align: center"><a id="copy-description" href="#"><p id="description"><?= $IP?><span class="fui-clip"></span></p></a></td>
            <td></td>
            <td></td>
        </tr>
        </table>

          <?php  }
        } else {
            ?><h3 style="text-align: center">No results found</h3><?php
        }
        ?>
    </div>
</body>