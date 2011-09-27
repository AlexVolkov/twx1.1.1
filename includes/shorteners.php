<?php
error_reporting(1);
class Shorteners {
    public $proxy = null;
    public function bitly ($url) {
        $connectURL = 'http://api.bit.ly/v3/shorten?login=masstest&apiKey=R_44403eb439622dd59ba2598255f30824&uri=' . urlencode($url) . '&format=txt';
        return $this->curl_get_result($connectURL, NULL, FALSE);
    }
    public function googl ($url) {
        $connectURL = 'http://goo.gl/api/shorten';
        $post_fields = array("security_token" => "null",
                "url" => $url
        );
        return $this->curl_get_result($connectURL, $post_fields, TRUE);
    }
    public function any ($url) {
        //$url = urlencode($url);
        //echo $url;
        $func = array('bitly', 'googl');
        $sh = $func[array_rand($func, 1)];
	//var_dump($sh);
	$JSON = false;
	if($sh == 'googl')
	      $JSON = true;
        return $this->$sh($url, NULL, $JSON);
    }
    public  function isgd ($url) {
        $connectURL = 'http://is.gd/api.php?longurl='.urlencode($url);
        return $this->curl_get_result($connectURL, NULL, FALSE);
    }
    public  function hexio ($url) {
        $connectURL = 'http://hex.io/api-create.php?url='.urlencode($url);
        return $this->curl_get_result($connectURL, NULL, FALSE);
    }
    public function trIm ($url) {
        $connectURL = 'http://api.tr.im/v1/trim_url.xml?url='.urlencode($url);
        return $this->curl_get_result($connectURL, NULL, FALSE);

    }
    public function tinyurl ($url) {
        $connectURL = 'http://tinyurl.com/api-create.php?url='.urlencode($url);
        return $this->curl_get_result($connectURL, NULL, FALSE);

    }
    public function twurl ($url) {
        $connectURL = 'http://tweetburner.com/links';
        $post_fields = array("link[url]" => urlencode($url),
            "commit" => "Shorten%20it!");
        return $this->curl_get_result($connectURL, $post_fields, TRUE);

    }
    private function curl_get_result ($url, $postdata, $JSON) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        if(isset($postdata)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $postdata) );
        }
        if ($this->proxy) {
            //var_dump( $this->proxy );
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "60");
	    curl_setopt($ch, CURLOPT_PROXYUSERPWD,'abdurahman:lNfEB^@3');
	    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($ch, CURLOPT_TIMEOUT, "60");
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, "30");
            curl_setopt($ch, CURLOPT_TIMEOUT, "30");
        }
        $data = curl_exec($ch); 
        curl_close($ch);
        if($JSON) {
            $obj = json_decode($data);
            return $obj->{'short_url'};
        } else {
            return trim($data);
        }

    }

}
/*
$test = new Shorteners();
$url = file_get_contents('http://awmproxy.com/allproxy.php?good=1');
$url = explode("\n", $url);
$test->proxy = trim($url[0]);
$tmp = $test->any("http://ping.blogs.yandex.ru/RPC2");
var_dump($tmp);
*/
?>