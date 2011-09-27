<?php
    function ShowWindow($text, $class) {
        echo "<a href=\"#\" class=\"box corners ".$class."\" id=\"message\">

			".$text."
			</a>";
    }
  $id = urlencode($_POST['id']);
  $cookie = urlencode($_POST['cookie']);

  if($cookie == 'demo'){
        ShowWindow("You are not allowed to run tasks in demo mode", "error");
	die();
  }

  $act = urlencode($_POST['act']);
  shell_exec("php ./job.php ".$id." ".$cookie." ".$act." > /dev/null &");
  ShowWindow("task ".$id." has ".$act."ed", "success");
//system('id');

?>