<?php

class GetInfo {

    public $db;
    public $pref;
    public $gaSql;

    function __construct($path) {
        try {
            require_once './' . $path . 'db.php';
            $this->gaSql = $gaSql;
            $this->db = new PDO("mysql:host=" . $gaSql['server'] . ";dbname=" . $gaSql['db'], $gaSql['user'], $gaSql['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cookie = htmlspecialchars(trim($_COOKIE['TWX_member_zone']));
            $res = @file_get_contents("http://twindexator.com/pstatus.php?auth=76Fryu89dfT3&mkey=" . $cookie);


            if (($res != $cookie . '|1') AND ($cookie != "demo")) {
                header("Location:/?nokey");
                //die('PASHOL ATSUDA!');
            }
            /* $query = $this->db->query("SELECT `id` FROM `twx`.`keys` WHERE `id` = '" . $cookie . "';")->Fetch(PDO::FETCH_ASSOC);
              //$query = "SELECT IF(EXISTS (SELECT `id` FROM `twx`.`keys` WHERE `id` = '$cookie '), 1, 0) FROM DUAL;";
              //echo $query; die();
              if (!$query) {
              unset($query);
             // include_once('./includes/updater.php');
             // $updater = new Updater();
             // $updater->showTemplate();
              //die();
             // $updater->db = $this->db;
             // $updater->key = $cookie;
             // $updater->update();

              } */

            //Change THIS FOR NEW SQL!!!!!!

            $query = $this->db->query("SELECT `key` FROM `service`.`keys` WHERE `key` = '" . $cookie . "';")->Fetch(PDO::FETCH_ASSOC);
            //var_dump($query);
            if (!$query) {
                $key = $cookie;
                $tmp = $this->db->query("INSERT INTO `service`.`keys` (`id` ,`key`,`mail`,`status`) VALUES (NULL , '$key', '', '0');");
                $sql = file_get_contents('./sp/sql.sql');
                $sql = preg_replace("!%KEY%!si", $key, $sql);

                if (strlen($sql) < 10)
                    die('<p class="warn">Cannot open sql file</p>');
                $sql = explode(";", $sql);
                foreach ($sql as $req):
                    if (strlen($req) > 2) {
                        $query = $this->db->query($req);
                        if (!$query) {
                            die('<p class="warn">Cannot execute sql<br /> ' . mysql_error() . '</p>');
                        }
                    }
                endforeach;

                $this->pref = $cookie;
            } else { //var_dump($query['key']);
                $this->pref = $query['key'];
            }
        } catch (PDOException $e) {
            echo $e->getMessage(), "\n";
        }
    }

    function GetSplash() {
        $sql = "SELECT  (SELECT COUNT(*)  FROM  `cust_tables`.`" . $this->pref . "_tasks`) AS task,
			(SELECT COUNT(*)  FROM  `cust_tables`.`" . $this->pref . "_tasks` WHERE status = 'start') AS task_active,
			(SELECT COUNT(*)  FROM  `cust_tables`.`proxy`) AS proxy,
			(SELECT COUNT(*)  FROM  `cust_tables`.`" . $this->pref . "_accounts`) AS accs FROM dual";
        $info = $this->db->query($sql)->Fetch(PDO::FETCH_ASSOC);
        return $info;
    }

    function Settings() {
        $sql = ("SELECT * FROM `cust_tables`.`" . $this->pref . "_config`;");
        return $this->db->query($sql)->FetchAll(PDO::FETCH_ASSOC);
    }

    function GetId() {
        return('/var/www/scrpt70/clients');
    }

}

?>
