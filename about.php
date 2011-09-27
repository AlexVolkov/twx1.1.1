<?php
include_once './includes/info.php';
$info = new GetInfo('includes/');
$splash = $info->GetSplash();
$data = $info->Settings();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Twindexator - Settings</title>
        <link rel="stylesheet" type="text/css" href="./css/index.css" media="all" />
        <link rel="stylesheet" href="./css/smoothness/jquery-ui-1.8.4.custom.css" type="text/css"
              media="screen" />
        <link type="text/css" rel="stylesheet" href="./css/uniform.default.css" />
	<link REL="SHORTCUT ICON" HREF="./favicon.ico">
        <link rel="stylesheet" href="./css/default.css" type="text/css" media="screen" />
        <script type="text/javascript" src="./js/jquery.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="./js/jquery-ui.min.js" charset="utf-8"></script>
        <script src="./js/jquery.uniform.js" type="text/javascript"></script>


        <script type="text/javascript" charset="utf-8">
            function delcookie()
            {
                var tmp_date=new Date()-10;
                document.cookie="TWX_member_zone=;expires=Thu, 01-Jan-70 00:00:01 GMT;";
                document.cookie="PHPSESSID=;expires=Thu, 01-Jan-70 00:00:01 GMT;";
                window.location.href="http://clients.twindexator.com/?loggedout";

            }
            $(function() {

                $("#settsBut").click(function() {
                    var sData = $("form:first").serializeArray(); 
                    $.post("./includes/actions.php", {data: sData, act: "update", type: "config"},
                    function(data){
                        $("#showMessage").html(data);
                        $("#showMessage").css({display: "block"});
                        setTimeout(function(){
                            $('#showMessage').fadeOut('slow', function() {
                                // Animation complete
                                location.reload();
                            });

                        },3000);

                    });
                });


            });


        </script>

    </head>
    <body id="indexBody">
        <div id="showMessage" style="display: none;"></div>

        <div class="top-tail">
            <div class="main-width">
                <div id="header">
                    <div class="bind-top-tail">
                        <div class="bind-right-tail">
                            <div class="bind-bot-tail">
                                <div class="bind-left-tail">
                                    <div class="bind-left-top">
                                        <div class="bind-right-top">
                                            <div class="bind-right-bot">
                                                <div class="bind-left-bot">
                                                    <div class="bind-indent">

                                                        <div class="wrapper">
                                                            <div class="logo">
                                                                <a href="/"><img src="./i/logo.jpg" alt="TwindeXator
                                                                                 Get Your Site Indexed Within Minutes!" width="440" /></a>
                                                            </div>
                                                            <div class="cart">
                                                                <span>Tasks:<?php echo $splash['task'];?></span>
                                                                <span>Proxy:<?php echo $splash['proxy'];?></span>
                                                                <span>Accounts:<?php echo $splash['accs'];?></span>
                                                            </div>
                                                        </div>


                                                        <div class="row">
                                                            <div id="globalNav">
                                                                <ul>
                                                                    <li>
                                                                        <a href="./tasks.php">Tasks<span></span></a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="./accounts.php">Accounts<span></span></a>
                                                                    </li>
                                                                   <!-- <li>
                                                                        <a href="./cronjobs.php">CronJons<span></span></a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="./proxy.php">Proxy<span></span></a>
                                                                    </li>-->
                                                                    <li>
                                                                        <a href="./setts.php">Settings<span></span></a>
                                                                    </li>
                                                                    <li class="selected">
                                                                        <a href="./about.php">About<span></span></a>
                                                                    </li>
                                                                </ul>
                                                                <span class="logout">
                                                                    <a href="javascript:delcookie();" title="Log out">Log out <img src="./i/enter.gif" alt="Log out" /></a>
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="wrapper">
                                                            &nbsp;
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="banners">
                    <div class="bind-top-tail">
                        <div class="bind-right-tail">
                            <div class="bind-bot-tail">
                                <div class="bind-left-tail">
                                    <div class="bind-left-top">
                                        <div class="bind-right-top">
                                            <div class="bind-right-bot">
                                                <div class="bind-left-bot">
                                                    <div class="bind-indent2">

                                                        <div id="dt_example" style="margin:5px; padding:10px;">

<h1 style="text-align:center;">twindeXator</h1><br/>
<small style="text-align:center; display:block;">version 1.1</small><br/>
<a href="http://twindexator.com/license-agreement/" target="_blank" style="text-align:center; display:block;">SaaS (Software as a Service) License Agreement</a><br/><br/>

<p style="text-align:center; display:block;">Website: <a href="http://twindexator.com" target="_blank"> Twindexator</a> </p><br/>
<p style="text-align:center; display:block;">Help: <a href="http://forum.twindexator.com" target="_blank">Forum</a> </p><br/>


&copy; TWX Media Solutions, 2011



                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="footer">
<!--
		Copyright © 2010 &nbsp;&nbsp;
                    <a href="http://twindexator.com" target="_blank"> Twindexator</a> &nbsp;&nbsp; | &nbsp;&nbsp;
                    <a href="http://forum.twindexator.com" target="_blank">Forum</a>&nbsp;&nbsp; | &nbsp;&nbsp;
                    <a href="mailto:support@twindexator.com" target="_blank">support@twindexator.com</a>-->
                </div>
            </div>
        </div>
    </body>
</html>