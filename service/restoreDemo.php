<?
require_once '../includes/db.php';
$db = new PDO("mysql:host=".$gaSql['server'].";dbname=".$gaSql['db'], $gaSql['user'], $gaSql['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->query("USE `cust_tables`");
$db->query("DROP TABLE IF EXISTS `demo_tasks`");
$db->query("DROP TABLE IF EXISTS `demo_config`");
$db->query("DROP TABLE IF EXISTS `demo_accounts`");


$sql = file_get_contents('../sp/sql.sql');
		  $sql = preg_replace("!%KEY%!si", "demo", $sql);

						  if(strlen($sql) < 10)
						      die('<p class="warn">Cannot open sql file</p>');
						  $sql = explode(";", $sql);
						  foreach ($sql as $req):
						      if(strlen($req) > 2) { 
							  $query = $db->query($req);
							  if(!$query) {
							      die('<p class="warn">Cannot execute sql<br /> '.mysql_error().'</p>');
							  }
						  }
						  endforeach;

//delete all proxies with error after adding new one

//echo $cHour;
//print_r($allCron);

?>