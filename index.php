<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once './functions.php';
$SITE_URL = "http://localhost/torrent-crawler/"

?>

<!DOCTYPE html>
<html>
<head>
<title>The torrent-crawler</title>
</head>

<body>
    <b>Available Sites : </b><br/>
    <?php getconfigs(); ?>
    <?php if(count($sites) > 0) : ?>
    <hr/>
    <form method="POST" action="<?php echo $SITE_URL; ?>?action=run">
        <input type="text" size="50" name="search" placeholder="Type here ..."/>
        <input type="submit" value ="Run"/>
    </form>
    <?php
    if ((isset($_GET["action"]) == "run")) {
            run();
        }
    ?>
    <?php else : ?>
    No site to crawl. Too bad.
    <?php endif; ?>
</body>
</html>