<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$sites = array();

function getconfigs() {
$sites_file = fopen("./sites", "r");
$i = 0;
    while(!feof($sites_file)) {
        $line = fgets($sites_file);
        $infos = explode("$%%%$", $line);
        if (count($infos) == 3) {
            echo "* " . $infos[0]. ' - ' . $infos[1] .' ('. $infos[2].')<br/>';
            $i = array_push($GLOBALS["sites"], $infos[2]);
        }
    }
    echo "<br/>". $i ." site(s) to Crawl.<br/>";
    fclose($sites_file);
}

function run() {
    if (empty($_POST)) {
        echo "<br/>Error try again.";
        return false;
    }
    if (empty($_POST["search"])) {
        echo "<br/>No result, try again.";
        return false;
    }
    $sites = $GLOBALS["sites"];
    foreach ($sites as $site) {
        searchPirateBay($site);
    }
}

function searchPirateBay($siteurl) {
    $search = $_POST["search"];
    $filter = '/0/7/0/';
    $search_format = str_replace(' ', '+', $search);
    $url = $siteurl.'/search/'.$search_format.$filter;
    
    echo "Looking for <em><b>" . $search . "</b></em> on " . $url . '<br/>';
    
    // Curl start
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    $return = curl_exec($curl);
    curl_close($curl);
    // Curl end

    $dom = new DOMDocument();
    $dom->loadHTML($return);
    
    echo '<h3>5 Best torrent links :</h3><br/>';
    
    $resultQuery = '//table[@id="searchResult"]/tr/td//div[@class="detName"]';
    $linkQuery = $resultQuery;
    $xpath = new DOMXPath($dom);
    $searchResult = $xpath->query($resultQuery);
    $i = 0;
        foreach ($searchResult as $name) {
            if (++$i > 5)
                break;
           $nameStr = $name->nodeValue;
           echo '[ '.$nameStr.' ]';
           
           $searchLink = $xpath->query('//table[@id="searchResult"]/tr/td//*[contains(., "'.$nameStr.'")]//a/@href');
           foreach ($searchLink as $link) {
               echo '<br/><a href="http://'.$siteurl.$link->nodeValue.'"> Torrent Link </a>';
           }
           
           $searchMagnet = $xpath->query('//table[@id="searchResult"]/tr//*[contains(., "'.$nameStr.'")]//a/@href');
           foreach ($searchMagnet as $magnet) {
               if (strncmp($magnet->nodeValue, "magnet", 6) == 0) {
                echo '<br/><a href="'.$magnet->nodeValue.'"> Magnet link</a>';
               }
           }             
            echo '<hr/>';
        }

    }
?>