<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$SITE_URL = "http://localhost/torrent-crawler";
$SEP = '/$*$/';

function getconfigs() {
    $userfile = fopen("./user.txt", "r");
    $i = 0;
    $configs = null;
    while(!feof($userfile)) {
        $line = fgets($userfile);
        if ($line) {
            $configs[$i++] = $line;
        }
    }
    fclose($userfile);
    return ($configs);
}

function getconfigsnb() {
    $confs = getconfigs();
    $nb = count($confs);
    return ($nb);
}

function getsurveyinfos($line = null) {
    if(!$line) {
        return null;
    }
    $data = explode($GLOBALS['SEP'], $line);
    return $data;
}

function deletesurvey($id = -1) {
    if ($id == -1 || $id < 0) {
        echo 'Oups error deleting survey...';
        return ;
    }
    $configs = getconfigs();
    $configsnb = getconfigsnb();
    $newconfigs = array();
    for ( $i = 0; $i < $configsnb; ++$i ) {
        if ($i != $id) {
            $newconfigs[$i] = $configs[$i];
        }
    }
    file_put_contents("./user.txt", $newconfigs);
    header('Location: ' . $GLOBALS["SITE_URL"] );
    return ;
}

function addnewsurvey() {
    if (empty($_POST["query"])) {
         echo '<br/>Oups Nothing was added.';
         return ;
    }
    file_put_contents("./user.txt", $_POST["query"].$GLOBALS['SEP'].PHP_EOL, FILE_APPEND);
    header('Location: ' . $GLOBALS["SITE_URL"] );
    return ;
}

function refreshsurvey($survey) {
    file_put_contents("./user.txt", $survey);
}

function markresolved($string = null) {
    if (!$string) {
        echo 'Oups error marking survey ...';
        exit;
    }
    $string = str_replace(PHP_EOL, '', $string);
    $newconfig = $string.'OK'.$GLOBALS["SEP"].PHP_EOL;
    return ($newconfig);
}
?>