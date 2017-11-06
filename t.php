<?php

function whois_query($domain) {
// fix the domain name:
    $domain = parse_url(strtolower(trim($domain)));
    $domain = isset($domain['host']) ? $domain['host'] : (isset($domain['path']) ? $domain['path'] : "");
    $domain = preg_replace('/^www\./i', '', $domain);
//    echo "[fun-$domain]\r\n";
// split the TLD from domain name
    $_domain = explode('.', $domain);
    $lst = count($_domain) - 1;
    $ext = $_domain[$lst];

// You find resources and lists
// like these on wikipedia:
//
// http://de.wikipedia.org/wiki/Whois
//telnet whois.internic.net 43
    $servers = array(
        "biz" => "whois.neulevel.biz",
        "com" => "whois.internic.net",
        "us" => "whois.nic.us",
        "coop" => "whois.nic.coop",
        "info" => "whois.nic.info",
        "name" => "whois.nic.name",
        "net" => "whois.internic.net",
        "gov" => "whois.nic.gov",
        "edu" => "whois.internic.net",
        "mil" => "rs.internic.net",
        "int" => "whois.iana.org",
        "ac" => "whois.nic.ac",
        "ae" => "whois.uaenic.ae",
        "at" => "whois.ripe.net",
        "au" => "whois.aunic.net",
        "be" => "whois.dns.be",
        "bg" => "whois.ripe.net",
        "br" => "whois.registro.br",
        "bz" => "whois.belizenic.bz",
        "ca" => "whois.cira.ca",
        "cc" => "whois.nic.cc",
        "ch" => "whois.nic.ch",
        "cl" => "whois.nic.cl",
        "cn" => "whois.cnnic.net.cn",
        "cz" => "whois.nic.cz",
        "de" => "whois.nic.de",
        "fr" => "whois.nic.fr",
        "hu" => "whois.nic.hu",
        "ie" => "whois.domainregistry.ie",
        "il" => "whois.isoc.org.il",
        "in" => "whois.ncst.ernet.in",
        "ir" => "whois.nic.ir",
        "mc" => "whois.ripe.net",
        "to" => "whois.tonic.to",
        "tv" => "whois.tv",
        "ru" => "whois.ripn.net",
        "org" => "whois.pir.org",
        "aero" => "whois.information.aero",
        "nl" => "whois.domain-registry.nl"
    );

    if (!isset($servers[$ext])) {
        die('Error: No matching nic server found!');
    }

    $nic_server = $servers[$ext];

    $output = '';
	
	

    // connect to whois server:
    if ($conn = fsockopen($nic_server, 43, $errno, $errstr, 10)) {
        fputs($conn, $domain . "\r\n");
        while (!feof($conn)) {
            $output .= fgets($conn, 128);
        }
        fclose($conn);
    } else {
	print $domain . "-t---- $errstr ";sleep(1);
      //  die('Error: Could not connect to ' . $nic_server . '!');
    }

    return nl2br($output);
}











function addChar($strs, $chars) {
    $result = [];
    foreach ($strs as $str) {
        foreach ($chars as $char) {
            $result[] = $str . $char;
        }
    }
    return $result;
}


$asc = "a o e i u ai ei ui ao ou iu ie er an en in un ang eng ing ong";
$ascs = "q x zh ch sh r z c s y w";


$array = [ 'xiang', 'wei', 'xiang', 'fen','hu', 'dong', 'xiao', 'zan',  'dian',  'yun',  'ku', 
    'yun',  'xin',  'liao',  'ai',  'yi',  'er', 'you',  'ke',  'hui',  'tian',
    'hong','zhi', 'neng', 'lian', 'you','yun', 'heng', 'mei', 'zhong',  'wu', 'ji', 'xian',
'quan','ka','fan','fans',
    'sheng', 'tong', 'yuan',  'yi', 'qi', 'duo',  'wei', 'me', 'mi','meng',  'xiao', 'ren', 
    'o2o',  'zhao', 'zhang', 'liang', 'tian', 'shu', 'ju', 'lai','zheng', 'qiong', 'cai', 'fu', 'dian', 'ke', 'mo', 'ji', 'xian',  'sheng', 'tong', 'yuan',  'yi', 'qi', 'duo',  'wei', 'me', 'mi','meng',  'xiao', 'ren', 'zhao', 'zhang', 'liang', 'tian', 'shu', 'ju', 'lai','zheng', 'qiong', 'cai', 'fu', 'dian', 'ke', 'mo','ka', 'man', 'an', 'dong', 'ni', 'luo', 'bing', 'bin', 'bi', 'ken', 'de', 'mai', 'dan', 'lao', 'ao', 'nao', 'hai', 'cheng', 'xia', 'ge', 'le', 'bao', 'ye', 'xin', 'dong', 'a', 'jing', 'hu', 'qi', 'wang', 'wei', 'long', 'niu', 'yu', 'ku', 'shi', 'kong', 'hao', 'da', 'han', 'miao', 'xun', 'wo', 'la', 'hui', 'gua', 'pai', 'kai', 'jia', 'feng', 'gu', 'zu', 'ma', 'ba', 'hua', 'kan', 'hong' , 'dao', 'gou', 'du', 'teng', 'fei', 'te'];

$ii =0;
foreach ($array as $keys => $values) {
foreach ($array as $key => $value) {

    //  foreach ($array as $key1 => $value1) {
        // foreach ($array as $key2 => $value2) {
         //    foreach ($array as $key3 => $value3) {
                $d = "www." . $values.$value    . ".com";
			  // 	print $d . "\r\n";
                $e = whois_query($d);
 print "$d ". "\r\n";
                if (strstr($e, 'Date')) {
                    
                } else {
                    print "[$d] ". "\r\n";
                }
				
				$ii++;
				if($ii%60==0){	print 'zzz';
				sleep(5);
			
				}
           //  }
        // }
    //}
}
}

//$domain= "www.bitefu.net";
//echo whois_query($domain);
?>
