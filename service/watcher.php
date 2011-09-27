<?

require_once '../includes/db.php';
$db = new PDO("mysql:host=".$gaSql['server'].";dbname=".$gaSql['db'], $gaSql['user'], $gaSql['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//ask for all keys aviable
$db->query("USE `service`;");
$keys = $db->query("SELECT `key` FROM `keys` ;")->FetchAll(PDO::FETCH_ASSOC);

foreach($keys as $num=>$key):
  $ch = file_get_contents('http://twindexator.com/pstatus.php?auth=76Fryu89dfT3&mkey='.$key['key']);
  $ch = explode("|", $ch);
  if($ch[1] == '1')
      $worklist[] = $key['key'];
  $db->query("UPDATE `keys` SET `status` = ".$ch[1]." WHERE `key` = '".$key['key']."';");
endforeach;

$db->query("USE `cust_tables`;");
$keys = $worklist;
foreach($keys as $num=>$key):
  $allCron[$num]['data'] = $db->query("SELECT `id`, `cronIntval` FROM `".$key."_tasks` WHERE cronIntval != '';")->FetchAll(PDO::FETCH_ASSOC);
  $allCron[$num]['key'] = $key;
  //echo ("SELECT `id`, `cronIntval` FROM `".$key."_tasks` WHERE cronIntval != '';\r\n");
endforeach;

//var_dump($allCron);
//die();


//get current hour
(date('i') < 30)? $m = '0': $m = '30';
$cHour = (date('G')* 1);
//echo $cHour = 6;
//$m = 0;
//exec("killall php");
foreach($allCron as $num=>$val) {
    
    $key = $val['key'];
    foreach($val['data'] as $data): //print_r($data);
    $tCheck = ($cHour * 60) / $data['cronIntval'];
    if(is_integer($tCheck) AND ( ($data['cronIntval'] >30) AND ($m < 30) ) OR ($data['cronIntval'] == 30) ) {
        //system('bash -c "cd ../includes/ && php job.php '.$data['id'].' '.$key.' cronned > /dev/null 2>&1 &"'); echo ("task launch\r\n");// with flag --cronned
      $killPid = exec("ps ux | awk '/".$data['id']." ".$key." cronned/ && !/awk/ {print $2}'");
      if($killPid)
exec ("kill ".$killPid);
      //echo $num . ' ' . ('(cd /var/www/scrpt70/clients/includes/ && php job.php '.$data['id'].' '.$key.' cronned &) >> /dev/null 2>&1 '); echo ("\r\n");
   exec('(cd /var/www/scrpt70/clients/includes/ && php job.php '.$data['id'].' '.$key.' cronned &) >> /dev/null 2>&1 '); 
   sleep(0.1);
    }
    endforeach;
}

//die();
?>