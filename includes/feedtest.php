<?php


function TimeCompare($feed) {
    $feed = file_get_contents($feed);
    $file = file_get_contents("../cronjobs/151.cron");
    preg_match_all("!<pubDate>(.*?)<\/pubDate>!si", $feed, $out);
    preg_match_all("!<link>(.*?)<\/link>!si", $feed, $outs);
    preg_match_all("!<title>(.*?)<\/title>!si", $feed, $outss);
    $outs = array_slice($outs[1],1);
    $outss = array_slice($outss[1],1);
    $arr['url'] = $outs;
    $arr['lastmod'] = $out[1];
    $arr['title'] = $outss;
    //print_r($arr);
    $comArr = unserialize($file);
    //print_r($comArr);
    $compare = array_diff_assoc($arr['lastmod'], $comArr['lastmod']);
    //print_r($compare);
    foreach($compare as $num=>$val) {
        $r['url'][] = $arr['url'][$num];
        $r['lastmod'][] = $arr['lastmod'][$num];
        $r['title'][] = $arr['title'][$num];
    }
    $wrStr = serialize($arr);
    $file = file_put_contents("../cronjobs/151.cron", $wrStr);
    if(isset($r))
        return $r;

    return false;
}


$feeds = 'http://news.google.com/news?pz=1&cf=all&ned=us&hl=en&output=rss';
if(!file_exists('../cronjobs/151.cron')) {
    $feed = file_get_contents($feeds);
    preg_match_all("!<pubDate>(.*?)<\/pubDate>!si", $feed, $out);
    preg_match_all("!<link>(.*?)<\/link>!si", $feed, $outs);
    preg_match_all("!<title>(.*?)<\/title>!si", $feed, $outss);
    $outs = array_slice($outs[1],1);
    $outss = array_slice($outss[1],1);
    $arr['url'] = $outs;
    $arr['lastmod'] = $out[1];
    $arr['title'] = $outss;
    $wrStr = serialize($arr);
    $file = file_put_contents("../cronjobs/151.cron", $wrStr);
} else {
    $arr = TimeCompare($feeds);
    if(!$arr) {
        echo('no job for cron');
        die();
    }
}

print_r($arr);

?>