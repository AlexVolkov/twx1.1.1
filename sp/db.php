<?php
die();
$link = mysql_connect('localhost', 'golan76', '56yuD3Fss');
if (!$link) {
    die('Not connected : ' . mysql_error());
}

// make foo the current db
$db_selected = mysql_select_db('cust_tables', $link);
if (!$db_selected) {
    die ('Can\'t use foo : ' . mysql_error());
}

for($i = 0;$i < 10; $i++){
    
    $key = md5(date('r'));

$sql = file_get_contents('./sql.sql');
$sql = preg_replace("!%KEY%!si", $key, $sql);

                                if(strlen($sql) < 10)
                                    die('<p class="warn">Cannot open sql file</p>');
                                $sql = explode(";", $sql);
                                foreach ($sql as $req):
                                    if(strlen($req) > 2) {
                                        $query = mysql_query($req);
                                        if(!$query) {
                                            die('<p class="warn">Cannot execute sql<br /> '.mysql_error().'</p>');
                                        }
                                }
				endforeach;



//echo $query;
$tmp = mysql_query($query);
if(!$tmp)
  mysql_error();
$tmp = mysql_query("INSERT INTO `service`.`keys` (`id` ,`key`,`mail`,`status`) VALUES (NULL , '$key', '', '0');");
if(!$tmp)
  mysql_error();
echo $key."\r\n";
    sleep(1);
}

mysql_close($link);
?>