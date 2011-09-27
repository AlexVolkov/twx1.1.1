<?php
if(!isset($_POST['key'])){

if(isset($_GET['expired'])){
  $mess = 'Your session has been expired';
}
if(isset($_GET['loggedout'])){
  $mess = "You're logged out";
}
?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Twindexator - Member Area</title>
	<link REL="SHORTCUT ICON" HREF="./favicon.ico">
        <script type="text/javascript" src="./js/jquery.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="./js/jquery-ui.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="./js/jquery.ui.core.js"></script>
	<script src="./js/jquery.infieldlabel.min.js" type="text/javascript" charset="utf-8"></script>
	
	<script type="text/javascript" charset="utf-8">
 $(function() {

	  $("label").hover(
	    function () {
	      $(this).css("display", "none");
	    }
	  );

});	</script>


<style>
body {background: #2784bd url('./i/bg_big.jpg') repeat-x scroll;}
div, input {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 20px;
}
h2 {
    color: #FFECA4;
    font-size: 4em;
    font-weight: normal;
    letter-spacing: -3px;
    padding: 30px 0 10px;
    font-family: Helvetica,Arial;
text-shadow: 0 2px 1px rgba(0, 0, 0, 0.4);
left: 32%; top: 25%; position:absolute;
}
#keyContainer {
 left: 20%; top: 45%; position:absolute; z-index:1;
}
#keyContainer .field{ width:530px; height:50px; background: transparent url('./i/enter.png') no-repeat 0 0; border:0; padding: 0 10px; }
#keyContainer .button{ width:200px; height:50px; background: url("./i/enter.png") no-repeat scroll -10px -121px transparent; border:0;}
#keyContainer .button {
    font-size: 20px;
    font-weight: bold;
    cursor:pointer;
    color: #4F3400;
    text-shadow: 0 2px 1px rgba(255, 255, 255, 0.4);
}
label { position: absolute; top: 14px; left: 15px; font-size:20px; color:#777; z-index:2;}
p{position:absolute; color:#fff; outline: medium none;
    overflow: hidden;
    padding: 0 0 0 10px;
    text-decoration: none;
    text-shadow: 0 1px 0 #333;}
</style>
</head>
<body>
<h2>Members Area</h2>
<form name="login" method="post" id="keyContainer">
    <label for="key" style="opacity: 1;">Enter your key</label>
    <input type="text" name="key" class="field" />
    <input type="submit" class="button" value="Sign In"/>
<p><?php echo $mess;?></p>
</form>





</body>
</html>
<?php }
else {

$link = mysql_connect('localhost', 'golan76', '56yuD3Fss');
if (!$link) {
    die('Not connected : ' . mysql_error());
    }
    
    // make foo the current db
    $db_selected = mysql_select_db('service', $link);
    if (!$db_selected) {
        die ('Can\'t use foo : ' . mysql_error());
        }

$key = $_POST['key'];
$key = htmlspecialchars($key);

$query = @mysql_query("select `key` from `service`.`keys` where `key` = '".$key."';");
$res = @mysql_result($query, NULL);
//var_dump($res);
setcookie("TWX_member_zone", $res, time()+3600, "/planet7/");
header("Location: /planet7");
mysql_close($link);

}?>