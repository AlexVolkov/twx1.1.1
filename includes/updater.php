<?php

/*
 * This file update script for newest version
 * 
 */

error_reporting(1);

class Updater {

    public $key;
    public $sqlFile = "./sp/sql2.txt";
    public $db;
    public $gaSql;
    private $taskLimit = 100;
    private $accsLimit = 1000;

    function __construct() {
        include_once './includes/db.php';
        $this->db = new PDO("mysql:host=" . $gaSql['server'] . ";dbname=" . $gaSql['db'], $gaSql['user'], $gaSql['password']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function showTemplate() {
        $template = file_get_contents('./errors/wait.php');
        print($template);
    }

    public function update() {
        /*
          $comEx = file_get_contents($this->sqlFile);
          $query = $this->db->query($comEx);

          if (!$query) {
          die("something went wrong");
          }

          unset($query);
         */
         $this->db->query("INSERT INTO `twx`.`keys` (`id`, `status`, `valid_thru`, `owner_name`, `last_login`) VALUES ('" . $this->key . "', '1', NULL, NULL, NOW());");
        $query = "SELECT * FROM `cust_tables`.`" . $this->key . "_tasks` WHERE 1 = 1;";
        $q = $this->db->query($query)->FetchAll(PDO::FETCH_ASSOC);

        foreach ($q as $n => $v) {
            $mask = $this->GenerateMask();

            switch ($v["source"]) {
                case("tweets"):
                    $service = "twitter_single";
                    break;
                case("feeds"):
                    $service = "twitter_feed";
                    break;
                case("retweet"):
                    $service = "twitter_retweet";
                    break;
                case("follow"):
                    $service = "twitter_follow";
                    break;
            }

            if ($v["cronIntval"] == '')
                $v["cronIntval"] = 0;

            $s['service'] = $service;
            $s['accs'] = $v['used_accounts'];
            $s['shortener'] = $v['shortener'];
            $s['task_threads'] = 10;
            $s['task_creation_time'] = 'NOW()';
            $s['task_modified_time'] = 'NOW()';
            $s['task_last_launch'] = 'NOW()';  
            $s['task_ping_results'] = 'no';  
            $s['task_skip_accs_with_errors'] = 'yes';    
            $s['task_work_by_sitemap'] = 'no';                
            $s['task_grab_titles'] = 'no';  
            $s['strip_links'] = 'no';              
            $s['is_dripped'] = 'no';  
            
            
            $s = serialize($s);
            //var_dump(($s)); die();

            $tq = "INSERT INTO `twx`.`tasks` (
                                        `id` ,
                                        `key_id` ,
                                        `mask` ,
                                        `task_name` ,
                                        `task_data` ,
                                        `task_content` ,
                                        `task_cron_intval` ,
                                        `task_progress` ,
                                        `task_status` 

                                    ) VALUES (
                                        NULL , 
                                        '$this->key', 
                                        '$mask', 
                                        '$v[task_name]', 
                                        '$s' , 
                                        '$v[content]', 
                                        '$v[cronIntval]', 
                                        '$v[progress]', 
                                        '$v[status]'
                                    );";
            //var_dump($tq); die();
            unset($s);
            $this->db->query($tq);
        } //import tasks ends
        //die("321");

        $query = "SELECT * FROM `cust_tables`.`" . $this->key . "_accounts` WHERE 1 = 1;";
        $q = $this->db->query($query)->FetchAll(PDO::FETCH_ASSOC);

        foreach ($q as $n => $v) {
            $aq = "INSERT INTO `twx`.`accounts` (
                    `id` ,
                    `pair` ,
                    `service` ,
                    `error` ,
                    `key_id`
                )VALUES (
                     NULL , 
                     '$v[pair]' , 
                     'twitter' , 
                     '$v[error]', 
                     '$this->key'
               );";
            $this->db->query($aq);
        } //ends import accounts
        unset($qw);

        //print("accounts were imported\r\n");

        $query = "SELECT * FROM `cust_tables`.`" . $this->key . "_config` WHERE 1 = 1;";
        $qw = $this->db->query($query)->FetchAll(PDO::FETCH_ASSOC);
        foreach ($qw as $n => $v) {
            $aq = "INSERT INTO `twx`.`user_config` (
                    `parameter` ,
                    `value` ,
                    `key_id`
                )VALUES (
                     '$v[opt_name]' , 
                     '$v[opt_value]', 
                     '$this->key'
               );";
            $this->db->query($aq);
        } //ends import settings

        //print("settings were imported\r\n");
        //die("end");
        
        
    }

    private function GenerateMask() {
        //sleep(1);
        return substr(md5(rand(0, 10000000000)), -10);
    }

    public function CheckUpdates() {
        $query = $this->db->query("SELECT `id` FROM `twx`.`keys` WHERE `id` = '" . $this->key . "';")->Fetch(PDO::FETCH_ASSOC);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

//var_dump($sqlFile);
}

?>
