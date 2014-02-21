<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_errors', 0);
error_reporting(0);
include_once './config.php';
include_once 'phpmailer/class.phpmailer.php';
$usermail = $argv[0];

searchLoop();
//sendmail('lol');

function sendmail($tabcontent) {
    $to = $GLOBALS['usermail'];
    $subject = 'Torrent Crawler : New link found ! '.$tabcontent[0];
    foreach ($tabcontent as $content) {
        echo " LINKS : ".$content;
    }
    //mail($to, $subject, $tabcontent);
}

function searchLoop() {
    $configs = getconfigs();
    if ($configs) {
        $i = 0;
        foreach ($configs as $search) {
            $search = getsurveyinfos($search);
            if (count($search) < 3 && searchPirateBay($search[0])) {
                $configs[$i] = markresolved($configs[$i]);
            }
            ++$i;
        }
        refreshsurvey($configs);
    }
}

function searchPirateBay($search) {
    $siteurl='https://thepiratebay.se';
    $filter = '/0/7/0/';
    $search_format = str_replace(' ', '+', $search);
    $url = $siteurl.'/search/'.$search_format.$filter;
    
    echo "Looking for " . $search . " on " . $url . PHP_EOL;
    
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
    
    
    $resultQuery = '//table[@id="searchResult"]/tr/td//div[@class="detName"]';
    $xpath = new DOMXPath($dom);
    $searchResult = $xpath->query($resultQuery);
    $mailcontent = null;
    $found = false;
    $i = 0;
        foreach ($searchResult as $name) {
                $nameStr = $name->nodeValue;
                if (!empty($nameStr)) {
                    $found = true;
                    echo 'RESULT FOUND :)'.PHP_EOL;
                    array_push($mailcontent, $nameStr); 
                }

                $searchLink = $xpath->query('//table[@id="searchResult"]/tr/td//*[contains(., "'.$nameStr.'")]//a/@href');
                foreach ($searchLink as $link) {
                    $link = $siteurl.$link->nodeValue;
                    array_push($mailcontent, $link);
                }

                $searchMagnet = $xpath->query('//table[@id="searchResult"]/tr//*[contains(., "'.$nameStr.'")]//a/@href');
                foreach ($searchMagnet as $magnet) {
                    $magnet = $magnet->nodeValue;
                    echo $magnet.PHP_EOL;
                    array_push($mailcontent, $magnet);
                }
                if ($found) {
                    sendmail($mailcontent);
                    break;
                }
           }
        return ($found);
    }
    
?>