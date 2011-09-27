<?php

error_reporting(0);

class Shorteners {

    public $proxy = null;

    public function any($url) {
        //$func = array('bitly', 'googl');
        $func = get_class_methods($this);
	array_shift($func);
	array_pop($func);
	//var_dump($func);
        $sh = $func[array_rand($func, 1)];
        //var_dump($sh);
	return $this->$sh($url, NULL);
    }

    public function bitly($url) {
        $connectURL = 'http://api.bit.ly/v3/shorten?login=masstest&apiKey=R_44403eb439622dd59ba2598255f30824&uri=' . urlencode($url) . '&format=txt';
        return $this->curl_get_result($connectURL, NULL);
    }

    public function googl($url) {
        $connectURL = 'http://goo.gl/api/shorten';
        $post_fields = "security_token=null&url=". urlencode($url);
        $res = $this->curl_get_result($connectURL, $post_fields); 
        $obj = json_decode($res);
        $ans = (!empty($res)) ? $obj->{'short_url'} : false;
        return $ans;
    }

    public function isgd($url) {
        $connectURL = 'http://is.gd/api.php?longurl=' . urlencode($url);
        return $this->curl_get_result($connectURL, NULL);
    }

    public function tinyurl($url) {
        $connectURL = 'http://tinyurl.com/api-create.php?url=' . urlencode($url);
        return $this->curl_get_result($connectURL, NULL);
    }
/*
    public function dlmn($url) {
        $connectURL = 'http://dlmn.org/submit/?url=' . urlencode($url) . '&ajax=false';
        $page = $this->curl_get_result($connectURL, NULL);
        preg_match("/<input id=\"dlmn-loc\" value=\"(.*?)\"/si", $page, $out);
        if (strlen($out[1]) < 3) {
            return false;
        } else {
            $answer = (empty($out[1])) ? false : "http://".$out[1];
            return $answer;
        }
    }
*/
    public function crum($url) {
        $connectURL = 'http://crum.bs/api.php?function=simpleshorten&url=' . urlencode($url);
        return $this->curl_get_result($connectURL, NULL);
    }

/*    public function ndurl($url) {
        $connectURL = 'http://www.ndurl.com/api.generate/?url=' . urlencode($url) . '&type=web';
        $res = $this->curl_get_result($connectURL, NULL); 
        $obj = json_decode(stripslashes($res));
        $obj = $obj->data;
        $ans = (!empty($res)) ? $obj->{'shortURL'} : false;
        return "7".$ans;
    }
*/
    public function qr($url) {
        $connectURL = 'http://qr.cx/api/?longurl=' . urlencode($url);
        return $this->curl_get_result($connectURL, NULL);
    }

/*    public function gatorurl($url) {
        $connectURL = 'http://gatorurl.com/api/rest.php?url=' . urlencode($url);
        return $this->curl_get_result($connectURL, NULL);
    }
*/
    public function jmb($url) {
        $connectURL = 'http://jmb.tw/api/create/?newurl=' . urlencode($url);
        return $this->curl_get_result($connectURL, NULL);
    }

    public function linkee($url) {
        $connectURL = 'http://api.linkee.com/1.0/shorten?input=' . urlencode($url);
        $res = $this->curl_get_result($connectURL, NULL);
        $obj = json_decode($res);
        $ans = (!empty($res)) ? $obj->{'result'} : false;
        return $ans;
    }

   /* public function metamark($url) {
        $connectURL = 'http://metamark.net/api/rest/simple?long_url=' . urlencode($url);
        return $this->curl_get_result($connectURL, NULL);
    }*/

    public function mtny($url) {
        $connectURL = 'http://mtny.mobi/api/?url=' . urlencode($url) . '&ismobile=false&type=simple';
        return $this->curl_get_result($connectURL, NULL);
    }

 /*   public function cli($url) {
        $connectURL = 'http://cli.gs/cligs/new';
        $post = "URL=" . urlencode($url);
        $page = $this->curl_get_result($connectURL, $post);
        preg_match("/Your new clig has been created/si", $page, $out);
        if (strlen($out[0]) < 3) {
            return false;
        } else {
            preg_match("#http://twitter.com/home\?status=(.*?)\"#si", $page, $out);
            $answer = (empty($out[1])) ? false : $out[1];
            return $answer;
        }
    }
*/
    private function curl_get_result($url, $postdata) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        if (isset($postdata)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }
        if ($this->proxy) {
            //var_dump( $this->proxy );
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "60");
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'abdurahman:lNfEB^@3');
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($ch, CURLOPT_TIMEOUT, "60");
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "30");
            curl_setopt($ch, CURLOPT_TIMEOUT, "30");
        }
        $data = curl_exec($ch);
        curl_close($ch);

        return trim($data);
    }

}

/*$test = new Shorteners();
$url = "http://google.com/reader";
var_dump($test->bitly($url));
var_dump($test->bitly($url));
var_dump($test->bitly($url));

/*$class_methods = get_class_methods('Shorteners');
foreach ($class_methods as $method):
    echo $method . "  " . $test->$method($url) . "\r\n";
endforeach;
*/
?>
