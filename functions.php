<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_errors', 0);
error_reporting(0);
include_once './config.php';
if ($argc != 2)
{
    echo 'USAGE : '.PHP_EOL.'$ php functions.php [user@email.com]'.PHP_EOL;
    exit;
}
$usermail = $argv[1];

searchLoop();

//sendmail('lol');

function sendmail($tabcontent) {
    $to = $GLOBALS['usermail'];
    $subject = 'Torrent Crawler : New link found ! '.$tabcontent[0];
    $body = '';
    foreach ($tabcontent as $content) {
        $body .= $content.PHP_EOL.PHP_EOL; 
    }
    echo 'DEST : '.$to.PHP_EOL;
    echo 'SUBJECT '.$subject.PHP_EOL;
    echo 'MAIL TO BE SEND : '.$body;
    return mail($to, $subject, $body);
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
            refreshsurvey($configs);
        }
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
    echo "fuck";
    
    $resultQuery = '//table[@id="searchResult"]/tr/td//div[@class="detName"]';
    $xpath = new DOMXPath($dom);
    $searchResult = $xpath->query($resultQuery);
    $mailcontent = array();
    $found = false;
    $i = 0;
        foreach ($searchResult as $name) {
                $nameStr = $name->nodeValue;
                if (empty($nameStr)) {
                    echo 'NO RESULT FOUND :('.PHP_EOL;
                    break;
                }
                
                array_push($mailcontent, $nameStr); 

                $searchLink = $xpath->query('//table[@id="searchResult"]/tr/td//*[contains(., "'.$nameStr.'")]//a/@href');
                foreach ($searchLink as $link) {
                    $link = $siteurl.$link->nodeValue;
                    array_push($mailcontent, $link);
                }

                $searchMagnet = $xpath->query('//table[@id="searchResult"]/tr//*[contains(., "'.$nameStr.'")]//a/@href');
                $cnt = 0;
                foreach ($searchMagnet as $magnet) {
                    $magnet = $magnet->nodeValue;
                    echo $magnet.PHP_EOL;
                    if (!strncmp($magnet, 'magnet', 6)) {
                        array_push($mailcontent, $magnet);
                    }
                    
                    echo 'RESULT FOUND :)'.PHP_EOL;
                }
                    $found = sendmail($mailcontent);
                    break;
           }
        return ($found);
    }
    
?>