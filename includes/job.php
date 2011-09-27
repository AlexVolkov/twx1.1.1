<?php
require_once './small.php';
require_once './shorts.php';

class Job {
    public $tryProxyCount;              //how much times try to yse one proxy??
    public $id;                         //job id
    public $verbose;                    //set details on(1) or off(0)
    public $judge;                      //url to check proxy before use
    public $useProxy;                   //using proxy
    public $useShort;                   //using shortners
    public $uid;			//user key
    private $useProxyError;             //use proxy with errors
    private $accsError;                 //use accounts with errors
    private $db;                        //database handler
    private $task;                      //task config
    private $config;                    //global config
    private $accounts;                  //loaded accounts


    function __construct() { 
        try {
            require_once './db.php';
            $this->db = new PDO("mysql:host=".$gaSql['server'].";dbname=".$gaSql['db'], $gaSql['user'], $gaSql['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
            echo $e->getMessage(), "\n";
        }
	if($this->uid == 'demo')
		die('demo task run');

    }
    public function LoadTask() {
	
        @unlink('../tmp/' . $this->id . '_' . $this->uid . '.txt');
	@unlink('../tmp/' . $this->id . '_' . $this->uid .'-links.txt');
        $sql = "SELECT `source`,`used_accounts`,`ordering`, `content`, `shortener` FROM `".$this->uid."_tasks` WHERE `id` = '$this->id';";
        $task = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
        $sql = "SELECT `opt_name`, `opt_value` FROM `".$this->uid."_config`";
        $config = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $this->accsError = $config[5]['opt_value'];
        $this->useProxyError = $config[4]['opt_value'];
        $this->useProxy = $config[1]['opt_value'];
        $this->useShort = $config[0]['opt_value'];
        $this->config = $config;
        $this->task = $task;
        $this->Logging('start task id '.$this->id);
        $this->Logging('using proxy is '.$this->useProxy
                . ', use short services is '.$this->useShort
                .', loading accounts with error is '.$this->accsError. ', shorters is '.$this->useShort);
        return $this->task;
    }
    public function LoadAccs() {
        ($this->accsError == "on")? $errors = "1" : $errors = "error='good' OR error=''";                          //errors
        ($this->task['used_accounts'] == "0")? $limit = "" : $limit = "LIMIT " . $this->task['used_accounts'];      //num of accounts to use in
        ($this->task['ordering'] == "order")? $order = "" : $order = "ORDER BY RAND()";                             //ordering
        $sql = "SELECT * FROM `".$this->uid."_accounts` WHERE $errors $order $limit;";
        var_dump($sql);
        $r = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $this->accounts = $r;//not needed
        $this->Logging('total accounts - '. count($this->accounts));
        return $r;
    }
    public function LoadProxy() {//var_dump($this->useProxy);
        if($this->useProxy == 'on') {
            $a = true;
            while ($a == true) {
                //($this->useProxyError == "on") ?  $errors = "1" : $errors = "error=''";
		($this->useProxyError == "on") ?  $errors = "1" : $errors = "1";
                $sql = ("SELECT `pair` FROM `proxy` WHERE " . $errors . " ORDER BY RAND() LIMIT 1;"); //echo $sql;
                //$p = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$p = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
                //var_dump($p);
                if(strlen($p['pair']) < 2)
                    die('no proxy found');
                //shuffle($p);
                $p = $p['pair']; 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->judge);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "10");
                curl_setopt($ch, CURLOPT_TIMEOUT, "10");
		//curl_setopt($ch, CURLOPT_PROXYUSERPWD,'abdurahman:lNfEB^@3');
                curl_setopt($ch, CURLOPT_PROXY, $p);
                curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5); 
		//curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "proxy=" . $p);
                $data = curl_exec($ch); 
		//var_dump($data);
                curl_close($ch);
                preg_match("!" . $p . "!si", $data, $out);
                if (strlen(@$out[0]) > 1) {  //proxy checking were changed, now its disabled
                    $a = false;
                    return trim($p);
                    break;
                }
                $this->SetError('proxy','error', $p);
                //$this->Logging('bad proxy '.$p);
            }
        } else {
            return false;
        }
    }
    public function TimeCompare($feed) {
        $feed = file_get_contents($feed); //var_dump($feed);
        $file = file_get_contents("../cronjobs/" . $this->uid . "_".$this->id.".cron"); //var_dump($file);
        preg_match_all("!<pubDate>(.*?)<\/pubDate>!si", $feed, $out);
        preg_match_all("!<link>(.*?)<\/link>!si", $feed, $outs);
        preg_match_all("!<title>(.*?)<\/title>!si", $feed, $outss);
        $outs = array_slice($outs[1],1);
        $outss = array_slice($outss[1],1);
        $arr['url'] = $outs;
        $arr['lastmod'] = array_slice($out[1],1);
        $arr['title'] = $outss;
        print_r($arr);
        $comArr = unserialize($file);
        //print_r($comArr);
        $compare = array_diff_assoc($arr['lastmod'], $comArr['lastmod']);
        print_r($compare);
        foreach($compare as $num=>$val) {
            $r['url'][] = $arr['url'][$num];
            $r['lastmod'][] = $arr['lastmod'][$num];
            $r['title'][] = $arr['title'][$num];
        }
        $wrStr = serialize($arr);
        $file = file_put_contents("../cronjobs/".$this->uid."_".$this->id.".cron", $wrStr);
        if(isset($r))
            return $r;

        return false;
    }
    public function Logging ($message) { //maybe private?
        $logmess = date("r") . " " . $this->id . " " . $message . " " . $this->echo_memory_usage() ."\r\n";
        if ($this->verbose == true)
            echo $logmess;
            $filel = fopen('../tmp/'. $this->id. '_'. $this->uid . '.txt', 'a+');
	    chmod('../tmp/'. $this->id. '_'. $this->uid . '.txt', intval('666', 8));
	    fwrite($filel, $logmess);
	    fclose($filel);
    }
    public function ChangeProgress ($val, $absolute) {
	$percent = $val;
	if(!$absolute)
	    $percent = (($val * 100) / count($this->accounts));
        $sql = ("UPDATE `".$this->uid."_tasks` SET `progress` = '$percent' WHERE `".$this->uid."_tasks`.`id` = '$this->id'");
        $this->db->query($sql);
    }
    public function SetError ($type, $mess, $value) {
        ($type !== "proxy")? $t = "`".$this->uid."_accounts`.`pair`" : $t = "`proxy`.`pair`";
        $sql = ("UPDATE `$type` SET `error` = '$mess' WHERE $t = '$value'"); 
        $this->db->query($sql);
	unset($sql);
    }
    public function CheckStatus () {
        $sql = ("SELECT `status` FROM `".$this->uid."_tasks` WHERE `".$this->uid."_tasks`.`id` = '$this->id'");
        $status = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
        if (($status['status'] !== 'stop')) :
            return false;
        else :
            return true;
        endif;
    }
    function TweetCheck ($text) {
        preg_match("!http:\/\/(.*?)!si", $text, $out);
        if (mb_strlen(@$out[1]) > 1) :
            //link found in text
            $text = preg_replace("!http://" . preg_quote($out[1]) . "!", "", $text);
            $wordLimit = 140 - mb_strlen(' http://' . $out[1]);
            $text = mb_substr($text, 0, $wordLimit);
            $text = $text . 'http://' . $out[1];
        else :
            $text = mb_substr($text, 0, 140);
        endif;
        return $text;
    }
    public function EncodeEntities($str) {
	$trans = get_html_translation_table(HTML_ENTITIES);

	foreach($trans as $t=>$c){
		$ctr[$c] = $t; 
	}

	$ctr["&apos;"] = "'";

	$encoded = strtr($str, $ctr);

	return $encoded;
    }

    public function ChangeStatus ($status) { 
        $sql = ("UPDATE `".$this->uid."_tasks` SET `status` = '$status' WHERE `".$this->uid."_tasks`.`id` = '$this->id'");

        $this->db->query($sql);
    }
    function echo_memory_usage() {
	    $mem_usage = memory_get_usage(true);
	  
	    if ($mem_usage < 1024)
		return $mem_usage." bytes";
	    elseif ($mem_usage < 1048576)
		return round($mem_usage/1024,2)." kilobytes";
	    else
		return round($mem_usage/1048576,2)." megabytes";
	} 

}


$job = new Job();
$twitter = new Twitter();
$short = new Shorteners();

//$job->id = $argv[1];    //or set to post var
if($_POST){
    $job->id = urlencode($_POST['id']);
    $job->uid = urlencode($_POST['cookie']);
    $act = $_POST['act'];
}


if($argv){
      $job->id = $argv[1];
      $job->uid = $argv[2];
      $act = $argv[3];
}

if($act == 'stop'){ 
      $job->ChangeStatus('stop');
      die("task stopped\r\n");
}
    $twitter->id = $job->uid;

$job->verbose = true;
$job->judge = 'http://alexvolkov.ru/postcheck.php';
$job->tryProxyCount = 3;

$job->ChangeStatus('start');
$job->ChangeProgress('0', TRUE);
$task = $job->LoadTask();
$accs = $job->LoadAccs();
$cht = 0;                   //total progress
$succC = 0;                 //succesfull operation, int
//var_dump($task["shortener"]);
$tweets = ""; //declare tweets


//print_r($task);
if(($task['source'] == 'feeds')): 
    if((@$argv[3] == "cronned") && (file_exists('../cronjobs/'.$job->uid.'_'.$job->id.'.cron'))) {
        $arr = $job->TimeCompare(trim($task['content']));
//var_dump($arr);
        if(!$arr) {
	    $job->ChangeStatus('stop');
	    $job->ChangeProgress('100', TRUE);
            $job->Logging('no job for cron');
            die();
        }
    } else {
        //if no cron
        $feeds = explode("\n", $task['content']); 
        foreach ($feeds as $feed) {
            if (strlen($feed) > 0) { //var_dump($feed);
                $feed = file_get_contents(trim($feed));
                if(!$feed) {
                    $job->Logging('cannot load feed '.$feed);
		    chmod('../tmp/'. $job->id. '_'. $job->uid . '.txt', intval('666', 8));
                    continue;
                }
                preg_match_all("!<pubDate>(.*?)<\/pubDate>!si", $feed, $out);
                preg_match_all("!<link>(.*?)<\/link>!si", $feed, $outs);
                preg_match_all("!<title>(.*?)<\/title>!si", $feed, $outss);
                $outs = array_slice($outs[1],1);
                $outss = array_slice($outss[1],1);
                /*$arr['url'] = $outs;
                $arr['lastmod'] = $out[1];
                $arr['title'] = $outss;*/
		//$outs = array_unique($outs);
		foreach($outs as $o){
		      $arr['url'][] = $o;
		}
		//$out[1] = array_unique($out[1]);
		foreach($out[1] as $o){
		      $arr['lastmod'][] = $o;
		}
		//$outss = array_unique($outss);
		foreach($outss as $o){
		      $arr['title'][] = $o;
		}
		
            }
        }
//var_dump($arr);
        $wrStr = serialize($arr);
        $file = file_put_contents("../cronjobs/".$job->uid."_".$job->id.".cron", $wrStr);
    }
    foreach($arr['url'] as $num=>$str) {
        $tweets .= $arr['title'][$num]. ' '.$str ."\n";
}
endif;


if(($task['source'] == 'tweets')):
    $job->Logging("task type is ". $task['source']);
    chmod('../tmp/'. $job->id. '_'. $job->uid . '.txt', intval('666', 8));
    $tweets = $task['content'];
endif;

//var_dump($tweets);
$tweets = explode("\n", $tweets);


$job->useProxy = "on"; //forced turned on

foreach ($tweets as $tweet) {
    
    if ((strlen($tweet) > 0)) {
	//var_dump($p); die();
        preg_match("/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", $tweet, $out);
        if (strlen(@$out[0]) > 1) {
            $fLink = trim($out[0]);
            if($job->useShort == 'on') {
		$p = $job->LoadProxy();
		$short->proxy = $p;
                $shortLink = $short->$task["shortener"](trim($fLink));
            } else {
                $shortLink = $fLink;
            }

            $tweet = preg_replace("!" . preg_quote($fLink) . "!si", $shortLink, $tweet);

        }
        $tweet = preg_replace("/\n/", "", $tweet);
        $tweet = preg_replace("/\r/", "", $tweet);


        $tweet = $job->tweetCheck($tweet);
	$tweet = $job->EncodeEntities($tweet);
	var_dump($tweet);
        $linkArray[] = trim($tweet);
    }
}

$job->Logging("formed " . count($linkArray) . " items in post array");
chmod('../tmp/'. $job->id. '_'. $job->uid . '.txt', intval('666', 8));
if(($task['source'] == 'feeds') OR ($task['source'] == 'tweets')){
      if(count($linkArray) < 1){
	      $job->Logging("nothing to post in");
	      die();
	      }
}
//die();

$b = true;  // set on loop
$m = 0;     //compare proxy counts with this in
//forced proxy enabling

$job->useProxy = 'on';
//var_dump($job->useProxy);
($job->useProxy == 'on') ? $a = true : $a = false;  //enable or disable loop repeating
$tAccs = count($accs);
foreach($accs as $cht=>$acc):
    
    //check for task launch or stop
    $tmp2 = $job->CheckStatus();
    if($tmp2 == 'stop'){
	  $job->Logging("task stopped by user");
	  //$job->ShowWindow("Task stoppped", "error");
	  die();
	}

    if($task['source'] == "retweet"){
	$tw = $task['content'];
     } else {
      $tw = $linkArray[array_rand($linkArray, 1)];
    }

    $job->ChangeProgress($cht, FALSE); //setting percents

    $b = true;

    while($b == true) {

        $job->Logging('using '.$acc['pair']. ' ' . $cht . "/". $tAccs);
        $twitter->pair = $acc['pair'];
        if(($a != false)) {
            $p = $job->LoadProxy();
            $job->Logging('proxy '.$p);
            $twitter->proxy = $p;
    	    //var_dump($p);
        }
        $lg = $twitter->LogIn();
	//var_dump($lg);
        $job->Logging($lg . " " .$m);

        if($lg == 'suspend') {
            $job->SetError($job->uid.'_accounts', 'suspend', $acc['pair']);
            $b = false;
            $m = 0;
            continue(2);
        }

        if($lg == 'page does not exist') {
            $job->SetError($job->uid.'_accounts', 'not exist', $acc['pair']);
            $b = false;
            $m = 0;
            continue(2);
        }

        if($lg == 'login or pass is not correct') {
            $job->SetError($job->uid.'_accounts', 'not correct', $acc['pair']);
            $b = false;
            $m = 0;
            continue(2);
        }

        if($lg == 'no token found') {
            //$b = false;
            if($m < $job->tryProxyCount) {
                $m ++;
                echo $m;
                continue;
            }   else {
                $m = 0;
                $b = false;
//exec("echo '".$acc['pair']."' >> failaccs.txt");
                break;
            }

        }

        if($lg == 'can not load post page') {
            //$b = false;
            if($m < $job->tryProxyCount) {
                $m ++;
                echo $m;
                continue;
            }   else {
                $m = 0;
                $b = false;
//exec("echo '".$acc['pair']."' >> failaccs.txt");
                break;
            }

        }

        if($lg == 'zero size returned') {
            if($m < $job->tryProxyCount) {
                $m ++;
                echo $m;
                continue;
            }   else {
                $m = 0;
                $b = false;
//exec("echo '".$acc['pair']."' >> failaccs.txt");
                break;
            }
        }

	if(strlen($lg) < 7) {
            if($m < $job->tryProxyCount) {
                $m ++;
                echo $m;
                continue;
            }   else {
                $m = 0;
                $b = false;
//exec("echo '".$acc['pair']."' >> failaccs.txt");
                break;
            }
        }

        //if retweet
        if($task['source'] == 'retweet'):
            $twitter->retweet = $task['content'];
            $r = $twitter->ReTweet();
            if($r){
                $job->Logging('retweeted successfully');    
		$accR = explode(":", $acc['pair']);	    
		$filet = fopen('../tmp/'. $job->id. '_'. $job->uid . '-links.txt', 'a+');
		chmod('../tmp/'. $job->id. '_'. $job->uid . '-links.txt', intval('666', 8));
		fwrite($filet, "http://twitter.com/".$accR[0]."\n");
		fclose($filet);
		$b = false;
		$succC++;
		$m = 0;
	   }
        endif;
        //if follow
        if($task['source'] == 'follow'):
            $twitter->follow = $task['content'];
            $r = $twitter->Follow();
            if($r){
                $job->Logging('followed successfully');
		$acctw = explode(":", $acc['pair']);
		$filet = fopen('../tmp/'. $job->id. '_'. $job->uid . '-links.txt', 'a+');
		chmod('../tmp/'. $job->id. '_'. $job->uid . '-links.txt', intval('666', 8));
		fwrite($filet, $acctw[0]."\n");
		fclose($filet);
		$b = false;
		$succC++;
		$m = 0;
	    }
        //if feeds or tweets
        endif;

        if(($task['source'] == 'tweets') OR ($task['source'] == 'feeds')):
            $twitter->tweet = $tw;
            $r = $twitter->Post();

            var_dump($r);
            //if($r == "can not load post page") {
	    if(strlen($r) < 3){
                $job->Logging($r . " " .$m);
                if($m < $job->tryProxyCount) {
                    $m ++;
                    continue;
                }   else {
                    $m = 0;
                    $b = false;
//exec("echo '".$acc['pair']."' >> failaccs.txt");
                    break;
                }
            }
            else {
                $job->Logging($twitter->tweet . ' posted successfully');
/*







*/


$tmp = $twitter->pingIt("http://blogsearch.google.com/ping/RPC2", $r, $r, $p); //good
$tmp = $twitter->pingIt("http://ping.syndic8.com/xmlrpc.php", $r, $r, $p); //good
$tmp = $twitter->pingIt("http://ping.bloggers.jp/rpc/", $r, $r, $p); //good
$tmp = $twitter->pingIt("http://rpc.weblogs.com/RPC2", $r, $r, $p); //good
$tmp = $twitter->pingIt("http://www.blogpeople.net/servlet/weblogUpdates", $r, $r, $p); //good
$tmp = $twitter->pingIt("http://xping.pubsub.com/ping", $r, $r, $p); //good
$tmp = $twitter->pingIt("http://api.my.yahoo.co.jp/RPC2", $r, $r, $p); //good

//$tmp = $twitter->pingIt("http://ping.feedburner.com", $r, $r, $p); //bad
//$tmp = $twitter->pingIt("http://api.my.yahoo.com/RPC2", $r, $r, $p); //bad
//$tmp = $twitter->pingIt("http://topicexchange.com/RPC2", $r, $r, $p); //bad
//$tmp = $twitter->pingIt("http://rpc.pingomatic.com/", $r, $r, $p);//bad
//$tmp = $twitter->pingIt("http://api.moreover.com/RPC2", $r, $r, $p);//bad
//$tmp = $twitter->pingIt("http://rpc.technorati.com/rpc/ping", $r, $r, $p);//bad
//$tmp = $twitter->pingIt("http://ping.weblogalot.com/rpc.php", $r, $r, $p);//bad
//$tmp = $twitter->pingIt("http://bblog.com/ping.php", $r, $r, $p);//bad

		$filet = fopen('../tmp/'. $job->id. '_'. $job->uid . '-links.txt', 'a+');
		chmod('../tmp/'. $job->id. '_'. $job->uid . '-links.txt', intval('666', 8));
		fwrite($filet, $r."\n");
		fclose($filet);
                $b = false;
                $succC++;
                $m = 0;
        }
        endif;

	$job->SetError($job->uid.'_accounts', '', $acc['pair']); //remove this after a few days!!!!
        $b = false;
}
unset($accs[$cht]);
endforeach;

$job->ChangeStatus('stop');
$job->ChangeProgress('100', TRUE);
$job->Logging('finished, '. $succC . ' successful, '.$tAccs.' total');
?>
