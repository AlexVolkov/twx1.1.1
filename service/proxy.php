<?
require_once '../includes/db.php';
$db = new PDO("mysql:host=".$gaSql['server'].";dbname=".$gaSql['db'], $gaSql['user'], $gaSql['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$url = file_get_contents('http://awmproxy.com/allproxy.php?good=1');
//$url = file_get_contents('http://downteam.ru/qw.php');
$url = explode("\n", $url);
$db->query("DELETE FROM `proxy` WHERE 1 ;");
foreach($url as $ip){
                  $query = ("INSERT IGNORE INTO `proxy` (`id` ,`pair` ,`error`) VALUES ('' , '$ip', ''); ");
$db->query($query);
}
//delete all proxies with error after adding new one

//echo $cHour;
//print_r($allCron);

?>