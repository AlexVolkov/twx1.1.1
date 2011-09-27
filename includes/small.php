<?php

class Twitter {

    public $pair;
    public $tweet;
    public $proxy = null;
    public $retweet;
    public $follow;
    protected $token;
    public $id;

    protected function LoadPage($url, $postdata, $proxy) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($proxy) {
            //echo $proxy;
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "60");
            //curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'abdurahman:lNfEB^@3');
            //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
            curl_setopt($ch, CURLOPT_TIMEOUT, "60");
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "30");
            curl_setopt($ch, CURLOPT_TIMEOUT, "30");
        }
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_COOKIEFILE, '../cookies/' . $this->id . '_cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, '../cookies/' . $this->id . '_cookie.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent());
        if ($postdata) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//var_dump(curl_error($ch));
        return curl_exec($ch);
    }

    protected function Check($needle, $haystack) {
        //var_dump($haystack);
        preg_match("!You are being!si", $haystack, $out); //check for success login
        if ($out) {
            //echo 123;
            $haystack = $this->LoadPage("http://mobile.twitter.com/", NULL, $this->proxy);
            //var_dump($page);
            //echo "redirect cathed\r\n";
            //return "logged in succesfully, trying to post in";
        }
        
        
        //sleep(3);
        //$f = fopen("./tnp.txt", "w+");
        $needle = preg_split("!(http:(.*?) |http:(.*?)$)!si", $needle);
        //var_dump($needle);
        foreach ($needle as $n) {
            if ($needlelen < strlen($n)) {
                $needlelen = strlen($n);
                $temp_patt = $n;
            }
        }
        if (strlen($temp_patt) > 1)
            $needle = $temp_patt;
        //var_dump($temp_patt); //die();
        //fwrite  ($f, $haystack);
        //fclose($f);
        preg_match_all("!<a href=\"(.*?)\" class=\"status_link!", $haystack, $yout);
        $needle = addcslashes($needle, "/\+\!\?\)\(");
        $needle = trim(preg_replace("!(http:(.*?) |http:(.*?)$)!si", "", $needle));
        $needle = preg_replace("!\#(.*?) !si", "", $needle);
        $needle = preg_replace("!@(.*?) !si", "", $needle);
        preg_match("/" . $needle . "/si", $haystack, $out);

        //var_dump($haystack);
        //var_dump($needle);
        
        if (@$out[0]) {
            return 'http://twitter.com' . $yout[1][0];
            //return $yout[1][0];
        } else {
            return false;
        }
    }

    public function pingIt($url, $blogname, $blogurl, $proxy) {
        $request = xmlrpc_encode_request("weblogUpdates.ping", array($blogname, $blogurl));
        $ch = curl_init();
        if ($proxy) {
            //echo $proxy;
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "60");
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'abdurahman:lNfEB^@3');
            //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
            curl_setopt($ch, CURLOPT_TIMEOUT, "60");
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "30");
            curl_setopt($ch, CURLOPT_TIMEOUT, "30");
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }

    protected function makeClean() {
        if (file_exists('../cookies/' . $this->id . '_cookie.txt'))
            unlink('../cookies/' . $this->id . '_cookie.txt');
    }

    function useragent() {
        $user_agents = explode("\n", file_get_contents('../src/user_agents.txt'));
        if (is_array(@$user_agents)) {
            return trim($user_agents[array_rand($user_agents)]);
        } else {
            $s = rand(1, 4);
            switch ($s) {
                case 1:
                    $agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)";
                    break;
                case 2:
                    $agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)";
                    break;
                case 3:
                    $agent = "Opera/9.20 (Windows NT 6.0; U; en)";
                    break;
                case 4:
                    $agent = "Mozilla/4.8 [en] (Windows NT 6.0; U)";
                    break;
            }
            return $agent;
        }
    }

    public function LogIn() {
        $this->makeClean();
        $exp = explode(':', $this->pair);
        $page = $this->LoadPage("http://mobile.twitter.com/" . $exp[0], NULL, $this->proxy);

        //var_dump($page);
        if ((!$page) || (strlen($page) < 20))
            return "zero size returned";

        //block, where we are checking for errors
        preg_match("!view was suspended due to strange activity!si", $page, $out);
        if ($out)
            return "suspend";
        preg_match("!the profile you are trying to view has been suspended!si", $page, $out);
        if ($out)
            return "suspend";
        preg_match("!that page doesn't exist!si", $page, $out); //check for page existing
        if ($out) {
            return "page does not exist";
        }

        //preg_match("!<input name=\"authenticity_token\" type=\"hidden\" value=\"(*.?)\"!si", $page, $token);
        preg_match("!<input name=\"authenticity_token\" type=\"hidden\" value=\"(.*?)\" />!si", $page, $token);
        //var_dump($token);
        if (strlen(@$token[1]) > 1) {
            //var_dump($page);
            $this->token = trim($token[1]);
        } else {
            return 'no token found';
            //return false;
        }
        //endblock
        $page = strip_tags($page);

        //echo $page;
        //make log in
        $postvars = array(
            'authenticity_token' => $this->token,
            'username' => trim($exp[0]),
            'password' => trim($exp[1])
        );
        $page = $this->LoadPage("https://mobile.twitter.com/session", http_build_query($postvars), $this->proxy);
        //var_dump($page);
        //$page = strip_tags($page);
        preg_match("!You are being!si", $page, $out); //check for success login
        if ($out) {
            $page = $this->LoadPage("http://mobile.twitter.com/", NULL, $this->proxy);
            //var_dump($page);
            echo "redirect cathed\r\n";
            //return "logged in succesfully, trying to post in";
        }

        preg_match("!What's happening!si", $page, $out); //check for success login
        if ($out) {
            return "logged in succesfully, trying to post in";
        }
        preg_match("!Sign in information is not correct!si", $page, $out); //check for success login
        if ($out) {
            return "login or pass is not correct";
        }
        return false;
    }

    public function Post() {
        $postvars = array(
            'authenticity_token' => $this->token,
            'tweet[text]' => $this->tweet,
            'tweet[in_reply_to_status_id]' => '',
            'tweet[lat]' => '',
            'tweet[long]' => '',
            'tweet[place_id]' => '',
            'tweet[display_coordinates]' => '',
        );
        //var_dump($this);
        $page = $this->LoadPage("http://mobile.twitter.com/", http_build_query($postvars), $this->proxy);
        //var_dump($page);
        if ($page) {
            return $this->Check($this->tweet, $page);
        } else {
            //return 'can not load post page';
            return false;
        }
    }

    public function ReTweet() {
        $retweet = $this->retweet;
        preg_match("![0-9]+.!si", $retweet, $out);
        if (!$out[0])
            return false;
        $rtID = $out[0];

        $postvars = array(
            'authenticity_token' => $this->token,
            'last_url' => 'http://mobile.twitter.com'
        );

        $page = $this->LoadPage("http://mobile.twitter.com/statuses/" . $rtID . "/retweet", http_build_query($postvars), $this->proxy);
        //var_dump("http://mobile.twitter.com/statuses/" .$rtID);
        if ($this->Check('Retweet sent', $page)) {
            return true;
        } else {
            return false;
        }
    }

    public function Follow() {
        $follow = $this->follow;
        $postvars = array(
            'authenticity_token' => $this->token,
            'last_url' => '/' . $follow
        );
        $page = $this->LoadPage("http://mobile.twitter.com/" . $follow . "/follow", http_build_query($postvars), $this->proxy);
        if ($this->Check('Following', $page)) {
            return true;
        } else {
            return false;
        }
    }

}

/*
  $accounts = file('./accs.txt');
  while(1 < 2){
  shuffle($accounts);

  $pair = $accounts[0];
  //$pair = 'plazaelonl:12c1vg9zz';
  echo $pair."\r\n";

  $twitter = new Twitter();
  $twitter->pair = $pair;
  $twitter->tweet = 'RT @alexvolkov Watchdog для своих скриптов или проверка состояния процесса http://goo.gl/9ECq';
  $lg = $twitter->LogIn();
  var_dump($lg); die();
  if($lg)
  $post = $twitter->Post();
  var_dump($post);
  //if($post) {
  //  $twitter->retweet = 'http://twitter.com/AlexVolkov/status/12047923873316864';
  //   $rt = $twitter->ReTweet();
  //   var_dump($rt);
  //}
  //$twitter->follow = 'AlexVolkov';
  //echo $twitter->Follow();
  }
 */
?>
