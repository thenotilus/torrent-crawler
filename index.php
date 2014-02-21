<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once './config.php';

?>

<!DOCTYPE html>
<html>
<head>
<title>The torrent-crawler</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>

<body>
    <?php $configs = getconfigs() ;?>
    <h3>Add new survey :</h3>
    <form method=post action="<?php echo $SITE_URL ;?>/?action=add">
        <input type="text" name="query" size="35">
        <input type="submit" value="Add">
    </form>
    <br/>
    <b>Your surveys :</b><br/>
    <?php if (!$configs) : ?>
        No survey found <br/>
    <?php else : ?>
        <table>
        <?php $id = 0 ; ?> 
        <?php foreach($configs as $survey) : ?>
            <?php $survey = getsurveyinfos($survey); ?>
            <?php if (count($survey) > 2) : ?>
                [ RESOLVED ] - 
            <?php endif ; ?>
             <?php echo $survey[0] ;?>
                <a href="<?php echo $SITE_URL ;?>/?action=delete&id=<?php echo $id ;?>">(delete)</a><br/>
             <?php ++$id ;?>
        <?php endforeach ; ?>
        </table>
    <?php endif ; ?>

        <?php 
            if(isset($_GET['action']) && $_GET['action'] == "add") {
                addnewsurvey();
            }
            if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id'])) {
                deletesurvey($_GET['id']);
            }
        ?>

        <br/>
        <hr/>
        - If the program finds a link for your survey, this one is automatically deleted.
</body>
</html>