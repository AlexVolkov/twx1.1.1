<?php

error_reporting(1);

class Actions
{

    public $post;
    private $db;
    private $prefix;
    private $settings;
    private $list;
    private $domain_id;
    private $tableCols;
    private $tableColsMarker;

    function __construct()
    {
        try {
            require_once './info.php';
            require_once './shorts.php';
            $class_methods = get_class_methods('Shorteners');
            foreach ($class_methods as $method):
                $this->list .= "<option value=\"$method\" >" . $method . "</option>";
            endforeach;
            $info = new GetInfo('');
            $this->prefix = $info->pref;
            $this->db = $info->db;
            $this->settings = $info->Settings();
            $this->domain_id = $info->GetId();
        } catch (PDOException $e) {
            echo $e->getMessage(), "\n";
        }
    }

    public function addSome($where, $list)
    {
        $list = explode("\n", $list);
        foreach ($list as $num => $id):
            if (strlen($id) > 1):
                if (!strpos($id, ":")) {
                    $mess = "<br/>but, some lines isn't contains pair with colon <strong>:</strong><br />";
                    break;
                }
                $id = trim($id);
                $query = ("INSERT IGNORE INTO `" . $this->prefix . "_" . $where . "` (`id` ,`pair` ,`error`)
							VALUES ('' , '$id', ''); ");

                if ($this->db->query($query)) {
                    $mess = "";
                } else { //echo $query;
                    echo ShowWindow("Problems with database", "error");
                    break;
                }
            endif;
        endforeach;

        echo $this->ShowWindow($where . " added <br/>" . $mess, "success");
    }

    public function deleteSome($where, $list)
    { //var_dump($list);
        if (!$list) {
            echo $this->ShowWindow("Choose something to delete", "error");
            return false;
        }

        if ($list == '-1') {
            if ($this->db->query("DELETE FROM `" . $this->prefix . "_" . $where . "` WHERE 1")) {
                /*@unlink('../cronjobs/' . $this->uid . '_'.$this->id.'.cron');
            @unlink('../tmp/'. $this->id. '_'. $this->uid . '.txt');
            @unlink('../tmp/'. $job->id. '_'. $job->uid . '-links.txt');
            @unlink('../cookies/'.$this->id.'_cookie.txt');*/
                echo $this->ShowWindow("All " . $where . " deleted", "success");
                return true;
            } else {
                echo $this->ShowWindow("Problems with database", "error");
            }
            exit;
        }


        if ($list !== '') {
            $ids = explode("|", $list);
            foreach ($ids as $id) :
                if (strlen($id) > 0) :

                    if ($this->db->query("DELETE FROM `" . $this->prefix . "_" . $where . "` WHERE `id` = $id")) {
                        @unlink('../cronjobs/' . $this->prefix . '_' . $id . '.cron');
                        @unlink('../tmp/' . $id . '_' . $this->prefix . '.txt');
                        @unlink('../tmp/' . $id . '_' . $this->prefix . '-links.txt');
                        @unlink('../cookies/' . $id . '_cookie.txt');
                        echo $this->ShowWindow($where . " deleted", "success");
                    } else {
                        echo $this->ShowWindow("Problems with database", "error");
                        return false;
                    }

                endif;
            endforeach;
        }
    }

    public function deleteMass($where, $list)
    { //var_dump($list);
        if (!$list) {
            echo $this->ShowWindow("Choose something to delete", "error");
            return false;
        }

        if ($list == '-1') {
            if ($this->db->query("DELETE FROM `" . $this->prefix . "_" . $where . "` WHERE 1")) {
                echo $this->ShowWindow("All " . $where . " deleted", "success");
                return true;
            } else {
                echo $this->ShowWindow("Problems with database", "error");
            }
            exit;
        }


        if ($list !== '') {
            $ids = explode("&", $list);

            foreach ($ids as $id) :
                if (strlen($id) > 0) :
                    if (strpos($id, 'length') > 1)
                        continue;
                    $id = preg_replace("!=on!si", "", $id);
                    if ($this->db->query("DELETE FROM `" . $this->prefix . "_" . $where . "` WHERE `id` = $id")) {
                        echo $this->ShowWindow($where . " deleted", "success");
                    } else {
                        echo $this->ShowWindow("Problems with database", "error");
                        return false;
                    }

                endif;
            endforeach;
        }
    }

    public function startMass($list)
    {
        if (!$list) {
            echo $this->ShowWindow("Choose something to start", "error");
            return false;
        }
        //var_dump($list);			
        if ($list !== '') {
            $ids = explode("&", $list);

            foreach ($ids as $id) :
                if (strlen($id) > 0) :
                    if (strpos($id, 'length') > 1)
                        continue;
                    $id = preg_replace("!=on!si", "", $id);
                    shell_exec("php ./job.php " . $id . " " . $this->prefix . " start > /dev/null &");

                endif;
            endforeach;
        }
        echo $this->ShowWindow("All tasks started", "success");
    }

    public function stopMass($list)
    {
        if (!$list) {
            echo $this->ShowWindow("Choose something to stop", "error");
            return false;
        }
        //var_dump($list);			
        if ($list !== '') {
            $ids = explode("&", $list);

            foreach ($ids as $id) :
                if (strlen($id) > 0) :
                    if (strpos($id, 'length') > 1)
                        continue;
                    $id = preg_replace("!=on!si", "", $id);
                    shell_exec("php ./job.php " . $id . " " . $this->prefix . " stop > /dev/null &");

                endif;
            endforeach;
        }
        echo $this->ShowWindow("All tasks stopped", "success");
    }

    public function addMassFeed($list)
    {
        //var_dump($list);
        $mask = substr(md5(date("r")), 0, 7);
        $sql = ("INSERT INTO `cust_tables`.`" . $this->prefix . "_drips` (
		`id` ,
		`name` ,
		`content` ,
		`cur_pos` ,
		`per_request` ,
		`mask`
		) VALUES (
		NULL , 
	        '$list[name]', 
		'$list[task_content]', 
		'$list[startfrom]', 
		'$list[chunk]', 
		'$mask'
		);");
        //echo $sql;
        //die();
        $this->db->query($sql); //execute query to add drip

        $sql = ("INSERT INTO `cust_tables`.`" . $this->prefix . "_tasks` (
	      `id` , 
	      `task_name`,
	      `source` ,
	      `used_accounts` ,
	      `ordering` ,
	      `progress` ,
	      `content` ,
	      `status` ,
	      `shortener` ,
	      `cronIntval`,
	      `mask`
	      ) VALUES (
	      NULL, 
	      '$list[name]',  
	      'feeds', 
	      '$list[numaccs]', 
	      'random', 
	      '0',
	      '" . $this->domain_id . "/dripped.php?task=$mask', 
	      'stop', 
	      '$list[shortener]', 
	      '$list[cronIntval]',
	      '$mask'
	      );");
        //echo $sql;
        $this->db->query($sql); //execute query to add feed in tasks
    }

    public function updateSome($where, $id, $new)
    {
        if ($this->db->query("UPDATE `" . $this->prefix . "_" . $where . "` SET `pair` = '$new' WHERE `id` = $id")) {
            //echo $this->ShowWindow($where . " deleted", "success");
        } else {
            //echo $this->ShowWindow("Problems with database", "error");
            return false;
        }
    }

    public function export($what)
    {
        $tmp = $this->db->query("SELECT * FROM `" . $this->prefix . "_" . $what . "`")->fetchAll(PDO::FETCH_ASSOC);
        header('Content-type: text/plain');
        foreach ($tmp as $str) {
            echo $str['pair'] . "\r\n";
        }
    }

    private function TweetCheck($text)
    {
        preg_match("/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", $text, $out);
        if (strlen($out[0]) > 5
        ) :
            //link found in text
            $text = trim(preg_replace("!" . preg_quote($out[0]) . "!si", "", $text));
            $wordLimit = 140 - strlen($out[0]);
            $text = substr($text, 0, $wordLimit);
            $text = $text . " " . $out[0];

        else :
            $text = substr($text, 0, 140);
        endif;
        return trim($text);
    }

    public function MultiVal($str)
    {
        $str = explode("\n", $str);
        foreach ($str as $s) {
            $mdata = $this->mashUp($s);
            foreach ($mdata as $m)
                $rs .= $m . "\n";
            if (strlen($rs) > 2) {
                $rs = preg_replace("/  /", " ", $rs);
                echo ($rs);
            }
        }
        //echo ($rs);
        //var_dump($str);
    }

    private function mashUp($str)
    {
        $limit = 1000;

        preg_match_all("/{(.*?)}/", $str, $out);
        $headArr = $out[1];

        for ($i = 0; $i < sizeof($headArr); $i++)
        {
            $valArr = preg_split("/(\||,|;)/", $headArr[$i]);
            ($i == 0) ? $sTotal = count($valArr) : $sTotal = $sTotal * count($valArr);
            $varr['text'][] = $valArr; //write down elements in each separate array
            $varr['change'][] = $sTotal; //write down changing sequence
        }

        if ($sTotal > $limit) {
            $sTotal = $limit;
        }

        for ($i = 0; $i < $sTotal; $i++) { //fill down array with blank values
            $strings[$i] = $str;
        }

        $limitCheck = 0;

        foreach ($headArr as $num => $val):
            $currPos = 0;
            $sChange = $sTotal / $varr['change'][$num]; //determine changing number
            $elementPos = 0; //number of element, which will be inserted instead {}
            $elemArr = explode("|", $val);

            for ($qw = 0; $qw < $sTotal; $qw++)
            {
                if ($currPos >= $sChange) {
                    $elementPos++;
                    $currPos = 0;
                }

                if ($elementPos >= (count($elemArr))) {
                    $elementPos = 0;
                }
                $strings[$qw] = preg_replace("!" . preg_quote("{" . $val . "}") . "!si", $elemArr[$elementPos], $strings[$qw], 1);
                $currPos++;
            }

        endforeach;
        return ($strings);
    }

    private function FeedCheck($url)
    {
        preg_match("/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/", $url, $out);
        if (strlen($out[0]) > 5) {
            return $url;
        } else {
            return false;
        }
    }

    public function addSomeTask($data)
    {
        //var_dump($data);
        $tname = htmlspecialchars(strip_tags($data['tw_name']));

        if (strlen($data['tweets']) > 1) {
            $tweets = explode("\n", $data['tweets']);
            foreach ($tweets as $text):
                $res .= addslashes($this->TweetCheck($text)) . "\n";
            endforeach;

            $sql = ("INSERT INTO `cust_tables`.`" . $this->prefix . "_tasks` 
(`id` , `task_name`,`source` ,`used_accounts` ,`ordering` ,`progress` ,`content` ,`status` ,`shortener` ,`cronIntval`)VALUES 
(NULL,'$tname', 'tweets', '$data[numaccs]', '$data[radio]', '0', '$res', 'stop', '$data[shortener]', '');");
            //$tname = htmlspecialchars(strip_tags($data['tw_name'])); 
            //var_dump($sql);
            //$res = $this->db->prepare($sql);
            $this->db->query($sql);
            //$res->execute();
            $this->ShowWindow("tweets added", "success");
        }
        if (strlen($data['feeds']) > 1) {
            //var_dump($data['tw_name']);

            $feeds = explode("\n", $data['feeds']);
            foreach ($feeds as $text):
                $ress = $this->FeedCheck($text);
                if ($ress)
                    $res .= $ress . "\r\n";
            endforeach;
            if (!$res) {
                continue(1);
            }
            $sql = ("
        	INSERT INTO `" . $this->prefix . "_tasks` (`id`, `task_name`, `source`, `used_accounts`, `ordering`, `progress`, `content`, `status`, `shortener`, `cronIntval`)
        	VALUES ('','$tname', 'feeds', '$data[numaccs]', '$data[radio]', '0', '$res', 'stop', '$data[shortener]', $data[cronIntval])
                    "); //echo $sql;
            $this->db->query($sql);
            $this->ShowWindow("feeds added", "success");
        }
        if (strlen($data['follow']) > 1) {
            //$tname = htmlspecialchars(strip_tags($data[f_name]));
            $sql = ("
        	INSERT INTO `" . $this->prefix . "_tasks` (`id`,`task_name`, `source`, `used_accounts`, `ordering`, `progress`, `content`, `status`, `shortener`, `cronIntval`)
        	VALUES ('','$tname', 'follow', '$data[numaccs]', '$data[radio]', '0', '$data[follow]', 'stop', 'none', $data[cronIntval])
                    ");
            $this->db->query($sql);
            $this->ShowWindow("followers will be increased", "success");
        }
        if (strlen($data['retweet']) > 1) {
            //$tname = htmlspecialchars(strip_tags($data[rtw_name]));
            $sql = ("
        	INSERT INTO `" . $this->prefix . "_tasks` (`id`,`task_name`, `source`, `used_accounts`, `ordering`, `progress`, `content`, `status`, `shortener`, `cronIntval`)
        	VALUES ('','$tname', 'retweet', '$data[numaccs]', '$data[radio]', '0', '$data[retweet]', 'stop', 'none', $data[cronIntval])
                    ");
            $this->db->query($sql);
            $this->ShowWindow("retweet task added", "success");
        }
    }

    public function editDrip($id)
    {
        $sql = ("SELECT `mask` FROM `" . $this->prefix . "_drips` WHERE `id` = '$id' LIMIT 1;");
        $mask = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($mask[0]);
        echo "<form id=\"editTask\">
		  <ul>";
        echo $this->LoadForm("drips", "`id` = " . $id);
        echo $this->LoadForm("tasks", "`mask` = '" . $mask[0]['mask'] . "'", array("content", "name", "mask", "id"));
        echo "		</ul>
	      </form>";
    }

    private function GetColumns($where)
    {
        if ($where)
            $this->tableColsMarker = $where;

        $sql = ("SHOW COLUMNS FROM `" . $this->prefix . "_" . $where . "`;");
        $this->tableCols = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function LoadForm($where, $cond, $exclude = NULL)
    { //where - which table, $cond = where contdition
        //var_dump($where, $id);
        $sql = ("SELECT * FROM `" . $this->prefix . "_" . $where . "` WHERE $cond LIMIT 1;");
        //echo $sql;
        $drip_data = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        if ($where != $this->tableColsMarker)
            $this->GetColumns($where);
        $col_data = $this->tableCols;
        //var_dump($drip_data);

        $what_to_show = array('content', 'cur_pos', 'per_request', 'name', 'used_accounts', 'ordering', 'shortener', 'cronIntval');
        $what_to_hide = array('id', 'mask');
        //$what_to_hide = array_merge($what_to_hide, $exclude);
        //var_dump($what_to_hide);
        foreach ($col_data as $field_num => $field_val) {
            if ((in_array($field_val["Field"], $what_to_show)) AND (!in_array($field_val["Field"], $exclude))) {

                if ($field_val['Field'] == "shortener") {
                    $shortsers = "<li><label>Select shortener:</label><select name=\"shortener\"> " . $this->list . "</select></li>";
                    echo preg_replace("!value=\"" . $drip_data[0][$field_val['Field']] . "\" >!si", "value=\"" . $drip_data[0][$field_val['Field']] . "\" selected>", $shortsers);
                    continue;
                }
                //var_dump($shortsers);


                if ($field_val['Field'] == "cronIntval") {
                    $cron = "
				      <li><fieldset id=\"cronset\" style=\"display:block;\">
								  <legend align=\"center\">Cron</legend>
								  <label>Launch every:</label><select name=\"cronIntval\">
								      <option value=\"\" >disable</option>
								      <option value=\"30\" >30 minutes</option>
								      <option value=\"60\" >1 hour</option>
								      <option value=\"180\" >3 hours</option>
								      <option value=\"360\" >6 hours</option>
								      <option value=\"720\" >12 hours</option>
								      <option value=\"1440\" >once a day</option>
								  </select>
							      </fieldset></li>";
                    $cron = preg_replace("!value=\"" . $drip_data[0][$field_val['Field']] . "\" >!si", "value=\"" . $drip_data[0][$field_val['Field']] . "\" selected>", $cron);
                    echo $cron;
                    continue;
                }

                //var_dump($field_val);
                preg_match("!char!si", $field_val['Type'], $out);
                if ($out[0]) {
                    $length = preg_replace("([^0-9])", "", $field_val['Type']);
                    //($length);
                    echo "<li><label>" . $field_val[Field] . "</label><input maxlength='" . $length . "' autocomplete='off' aria-required='true' type='text' name='$field_val[Field]' class='$field_val[Field]' value='" . $drip_data[0][$field_val['Field']] . "'/></li>";
                }

                preg_match("!text!si", $field_val['Type'], $out);
                if ($out[0]) {
                    echo "<li><label>" . $field_val[Field] . "</label><textarea name='$field_val[Field]' class='$field_val[Field]'>" . $drip_data[0][$field_val['Field']] . "</textarea></li>";
                }

                preg_match("!int!si", $field_val['Type'], $out);
                if ($out[0]) {
                    $length = preg_replace("([^0-9])", "", $field_val['Type']);
                    echo "<li><label>" . $field_val[Field] . "</label><input maxlength='" . $length . "' autocomplete='off' aria-required='true' type='text' name='$field_val[Field]' class='$field_val[Field]' value='" . $drip_data[0][$field_val['Field']] . "'/></li>";
                }

                preg_match("!enum\((.*?)\)!si", $field_val['Type'], $out);
                if ($out[0]) {
                    $tstr = str_replace("'", "\"", $out[1]);
                    $tstr = explode(",", $tstr);
                    $en = "<li>";
                    foreach ($tstr as $s) {
                        if (strlen($s) > 0) {
                            if ($s == "\"" . $drip_data[0][$field_val['Field']] . "\"") {
                                $en .= "<label>" . str_replace('"', '', $s) . "</label><input type=\"radio\" value=" . $s . " name=\"radio\" checked>";
                            } else {
                                $en .= "<label>" . str_replace('"', '', $s) . "</label><input type=\"radio\" value=" . $s . " name=\"radio\">";
                            }
                        }
                    }
                    $en .= "</li>";
                    echo $en;
                }
            }

            if (in_array($field_val["Field"], $what_to_hide)) {
                echo "<input type='hidden' name='$field_val[Field]' value='" . $drip_data[0][$field_val['Field']] . "'/>";
            }
        }
    }

    public function editTask($id)
    {
        $sql = ("SELECT * FROM `" . $this->prefix . "_tasks` WHERE `id` = '$id' LIMIT 1;");
        $tmp = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        //print_r($tmp); die();
        

        $valInt = $tmp[0]['cronIntval'];
        $shVal = $tmp[0]['shortener'];
        $rand = $ord = "";
        if ($tmp[0]['ordering'] == "random") {
            $rand = "checked";
        } else {
            $ord = "checked";
        }
        $cron = "
 <fieldset id=\"cronset\" style=\"display:block;\">
                            <legend align=\"center\">Cron</legend>
                            <label>Launch every:</label><select name=\"cronIntval\">
                                <option value=\"\" >disable</option>
                                <option value=\"30\" >30 minutes</option>
                                <option value=\"60\" >1 hour</option>
                                <option value=\"180\" >3 hours</option>
                                <option value=\"360\" >6 hours</option>
                                <option value=\"720\" >12 hours</option>
                                <option value=\"1440\" >once a day</option>
                            </select>
                        </fieldset>
";
        $cron = preg_replace("!value=\"" . $valInt . "\" >!si", "value=\"" . $valInt . "\" selected>", $cron);


        $shortsers = "<li><label>Select shortener:</label><select name=\"shortener\"> " . $this->list . "</select></li>";

        $shortsers = preg_replace("!value=\"" . $shVal . "\" >!si", "value=\"" . $shVal . "\" selected>", $shortsers);


        $formData = "<form id=\"editTask\" method=\"post\">
			<ul>
			<li>
			<label>Task name: </label> <input type=\"text\" name=\"task_name\" value=\"" . $tmp[0]['task_name'] . "\" style=\"width:90%;\" /><br/>
			<label>Used accounts:</label>
                        <input style=\"width:50px;\" type=\"text\" name=\"used_accounts\" value=\"" . $tmp[0]['used_accounts'] . "\"/></li>
			<input type=\"hidden\" name=\"id\" value=\"" . $tmp[0]['id'] . "\"/>
			<li><label>Choose accounts </label>
                        <input type=\"radio\" name=\"ordering\" value=\"order\" " . $ord . "/><small>in a sequence</small>
		<input type=\"radio\" name=\"ordering\" value=\"random\" " . $rand . "/><small>randomly</small></li>";
        if ($this->settings[0]['opt_value'] == "on") {
            $formData .= $shortsers;
        }
        $formData .= "<li><label>Source:</label><textarea name=\"content\">" . $tmp[0]['content'] . "</textarea></ul>
        ";
        if ($tmp[0]['source'] != "tweets") {
            $formData .= $cron;
        }
        $formData .= "</form>";

        echo $formData;
    }

    public function save($data, $where, $exclude = NULL)
    { //cored
        unset($str);
        $data = $this->splitArray($data);
        //var_dump($data);die();

        $marker = NULL;

        if (isset($data["id"])) {
            $marker = $data["id"];
            $f = "id";
        }
        if (isset($data["mask"])) {
            $marker = $data["mask"];
            $f = "mask";
        }
        //var_dump($marker);

        if (!$marker)
            die("no form marker");

        if ($where != $this->tableColsMarker)
            $this->GetColumns($where);
        $col_data = $this->tableCols;

        foreach ($col_data as $d) {
            if (array_key_exists($d["Field"], $data) AND (!in_array($d["Field"], $exclude))) {
                $str .= "`" . $d["Field"] . "` = '" . addslashes($data[$d["Field"]]) . "', ";
            }
        }
        $str = substr($str, 0, -2) . " ";
        $sql = "UPDATE `" . $this->prefix . "_" . $where . "` SET " . $str . " WHERE `" . $this->prefix . "_" . $where . "`.`" . $f . "` = '" . $marker . "';";
        //echo $sql; die();
        
        
        $tmp = $this->db->query($sql);
        if ($tmp) {
            $this->ShowWindow("succesfully updated", "success");
        } else {
            $this->ShowWindow("cannot perform query", "error");
        }
    }

    private function splitArray($data)
    {
        //var_dump($data);
        foreach ($data as $d) {
            $tmpAr1[] = $d["name"];
            $tmpAr2[] = $d["value"];
        }
        //var_dump($tmpAr1);
        return array_combine($tmpAr1, $tmpAr2);
    }

    public function saveData($data)
    {
        //var_dump($data);
        $id = $data[2][value];
        foreach ($data as $num => $string) {

            if ($string['name'] == 'content'):
                if (strlen($string['value']) > 3) {
                    $tweets = explode("\n", $string['value']);
                    foreach ($tweets as $text):
                        $res .= $this->TweetCheck($text) . "\n";
                    endforeach;
                }
                $string['value'] = $res;
            endif;
            if ($string['name'] == 'name'):
                $string['value'] = htmlspecialchars(strip_tags($string['value']));
            endif;
            if ($string['used_accounts'] == 'name'):
                $string['value'] = $string['value'] * 1;
            endif;
            if ($string['content'] == 'name'):
                $string['value'] = htmlspecialchars(strip_tags($string['value']));
            endif;

            $sql = ("UPDATE `" . $this->prefix . "_tasks` SET `$string[name]` = '" . $string['value'] . "' WHERE `id` = '$id';");
            //echo $sql;
            $tmp = $this->db->query($sql);
        }
        $this->ShowWindow("task updated", "success");
    }

    public function updateConfig($data)
    {

        $this->db->query("UPDATE `" . $this->prefix . "_config` SET `opt_value` = 'off' WHERE 1 = 1;");
        $this->db->query("UPDATE `" . $this->prefix . "_config` SET `opt_value` = 'on' WHERE `opt_name` = 'use_proxy';");
        foreach ($data as $num => $id):
            if ($id['name'] == "refresh_task_table_intval") {
                if (($id['value'] == '0') OR ($id['value'] == ''))
                    $id['value'] = 10;
            }
            $sql = ("UPDATE `" . $this->prefix . "_config` SET `opt_value` = '$id[value]' WHERE `opt_name` = '$id[name]';");
            $tmp = $this->db->query($sql);
            if (!$tmp) {
                echo ShowWindow("Problems with database", "error");
                break;
            }

        endforeach;
        $this->ShowWindow("Settings updated", "success");
    }


    public function ShowWindow($text, $class)
    {
        echo "<a href=\"#\" class=\"box corners " . $class . "\" id=\"message\">

			" . $text . "
			</a>";
    }

}

//var_dump($_POST);

$act = new Actions();
//var_dump($act);
//echo 123;
//die();
if (isset($_POST['filename'])) {
    $c = file_get_contents("../uploads/" . $_POST['filename']);
    $_POST['tweets'] = $c;
    unlink("../uploads/" . $_POST['filename']);
}

if ($_GET['act'] == 'export') {
    switch ($_GET['what']) {
        case("accs"):
            $act->export('accounts');
            break;
        case("proxy"):
            $act->export('proxy');
            break;
    }
}

if (!isset($_POST)) {
    $act->ShowWindow('no actions selected', 'error');
    die();
}

if ($_POST['act'] == 'add') {
    switch ($_POST['type']) {
        case("accs"):
            $act->addSome('accounts', $_POST['data']);
            break;
        case("proxy"):
            $act->addSome('proxy', $_POST['data']);
            break;
        case("task"):
            $act->addSomeTask($_POST);
            break;
    }
}

if ($_POST['act'] == 'delete') {
    switch ($_POST['type']) {
        case("accs"):
            $act->deleteSome('accounts', $_POST['data']);
            break;
        case("proxy"):
            $act->deleteSome('proxy', $_POST['data']);
            break;
        case("drips"):
            $act->deleteSome('drips', $_POST['data']);
            break;
        case("task"):
            $act->deleteSome('tasks', $_POST['data']);
            break;
    }
}


if ($_POST['act'] == 'update') {
    switch ($_POST['type']) {
        case("accs"):
            $act->updateSome('accounts', $_POST['recId'], $_POST['value']);
            break;
        case("proxy"):
            $act->updateSome('proxy', $_POST['recId'], $_POST['value']);
            break;
        case("config"):
            $act->updateConfig($_POST['data']);
            break;
    }
}

if ($_POST['act'] == 'edit') {
    switch ($_POST['type']) {
        case("task"):
            $act->editTask($_POST['id']);
            break;
        case("drip"):
            $act->editDrip($_POST['id']);
            break;
    }
}
if ($_POST['act'] == 'save') {
    switch ($_POST['type']) {
        case("task"):
            //$act->saveData($_POST['data']);
            $act->save($_POST['data'], "tasks");
            break;
        case("drip"):
            $act->save($_POST['data'], "drips");
            $act->save($_POST['data'], "tasks", array("content"));
            break;
    }
}
if ($_POST['act'] == 'deleteMass') {
    switch ($_POST['type']) {
        case("accs"):
            $act->deleteMass('accounts', $_POST['data']);
            break;
        case("proxy"):
            $act->deleteMass('proxy', $_POST['data']);
            break;
        case("task"):
            $act->deleteMass('tasks', $_POST['data']);
            break;
        case("drips"):
            $act->deleteMass('drips', $_POST['data']);
            break;
    }
}
if ($_POST['act'] == 'startAll') {

    $act->startMass($_POST['id']);
}

if ($_POST['act'] == 'stopAll') {

    $act->stopMass($_POST['id']);
}

if ($_POST['type'] == 'massfeed') {

    $act->addMassFeed($_POST);
}

if ($_POST['act'] == 'multiple') {
    $act->MultiVal($_POST['data']);
}
//var_dump($_POST);
//
// clear all fields after request
//all task have two groups - with id and with array values
?>
