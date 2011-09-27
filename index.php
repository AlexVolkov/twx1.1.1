<?php
$authurl = "http://twindexator.com/pstatus.php?auth=76Fryu89dfT3&mkey=";

//var_dump($_COOKIE);
if (isset($_COOKIE['TWX_member_zone'])) {

    $key = $_COOKIE['TWX_member_zone'];
    $key = htmlspecialchars(trim($key));

    if ($key == 'demo') {
        setcookie("TWX_member_zone", $key, time() + 36000, "/");
        header("Location: /tasks.php");
        die("demo");
    }


    $res = @file_get_contents($authurl . $key);
    //var_dump($res);
    if ($res == $key . '|1') {
        //var_dump($res);
        setcookie("TWX_member_zone", $key, time() + 36000, "/");
        header("Location: /tasks.php");
    }
    if ($res == $key . '|2')
        header("Location: /?freeze");
    if ($res == $key . '|8')
        header("Location: /?noexist");
    if ($res == $key . '|9')
        header("Location: /?notcorrect");
}

if (!isset($_POST['key'])) {

    if (isset($_GET['expired']))
        $mess = 'Your session has been expired';

    if (isset($_GET['noexist']))
        $mess = 'Key doesn\'t exist';

    if (isset($_GET['notcorrect']))
        $mess = "This is not a key";

    if (isset($_GET['loggedout']))
        $mess = "You're logged out";
    if (isset($_GET['freeze']))
        $mess = "Your key has been blocked";

    if (isset($_GET['dontknow']))
        $mess = "В душе не ебу, че это за ошибка";

    if (isset($_GET['nokey']))
        $mess = 'Your key is not valid';

    if (isset($_GET['expired']))
        $mess = 'Your session has been expired.';
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
                        font-family: Helvetica,Arial;
                        text-shadow: 0 2px 1px rgba(0, 0, 0, 0.4);
                        left: 32%; top: 100px; position:absolute;
                    }
                    #keyContainer {
                        left: 20%; top: 250px; position:absolute; z-index:1;
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

                <?php
//var_dump(stripos($_SERVER["HTTP_HOST"], "emo"));
                if (stripos($_SERVER["HTTP_HOST"], "emo") == '1') {
                    $inVal = "demo";
                    $text = "";
                } else {
                    $inVal = "";
                    $text = "Enter your key";
                }
                ?>


                <h2>Members Area</h2>
                <form name="login" method="post" id="keyContainer">
                <label for="key" style="opacity: 1;"><?php echo $text; ?></label>
                <input type="text" name="key" class="field" value="<?php echo $inVal; ?>" />
                <input type="submit" class="button" value="Sign In"/>
                <p><?php echo $mess; ?></p>
            </form>





        </body>
    </html>
    <?php
} else {

    $key = $_POST['key'];
    $key = htmlspecialchars(trim($key));
//echo 123;
    $res = @file_get_contents($authurl . $key);

    if (($res == $key . '|1') OR ($key == 'demo')) {
        setcookie("TWX_member_zone", $key, time() + 36000, "/");
        include_once('./includes/updater.php');
        /*$updater = new Updater();
        $updater->key = $key;
        $doUp = $updater->CheckUpdates();
        
        if (strlen($doUp) < 1) {
            $updater->showTemplate();
            $updater->update();
            //echo 123;
        } 
        */
        //die();
        
        header("Location: /tasks.php");
    }
    
    
    //other exceptions
    if ($res == '')
        header("Location: /?dontknow");
    if ($res == $key . '|8')
        header("Location: /?noexist");
    if ($res == $key . '|2')
        header("Location: /?freeze");
    if ($res == $key . '|9')
        header("Location: /?notcorrect");
}
?>
