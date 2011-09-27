<?php
//$message = "new shortening services are aviable";
include_once './includes/info.php';
include_once './includes/shorts.php';

$info = new GetInfo('includes/');

//die();
$splash = $info->GetSplash();
$data = $info->Settings();
$class_methods = get_class_methods('Shorteners');
foreach ($class_methods as $method):
    $list .= "<option value=\"$method\" >" . $method . "</option>";
endforeach;
//var_dump($list);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Twindexator - Tasks</title>
        <link rel="stylesheet" type="text/css" href="./css/index.css" media="all" />
        <link rel="stylesheet" href="./css/smoothness/jquery-ui-1.8.4.custom.css" type="text/css"
              media="screen" />
        <link type="text/css" rel="stylesheet" href="./css/uniform.default.css" />
        <link REL="SHORTCUT ICON" HREF="./i/favicon.ico" />
        <link rel="stylesheet" href="./css/default.css" type="text/css" media="screen" />
        <script type="text/javascript" src="./js/jquery.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="./js/jquery-ui.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="./js/jquery.ui.core.js"></script>
        <script type="text/javascript" src="./js/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="./js/jquery.ui.tabs.js"></script>
        <script type="text/javascript" src="./js/jquery.dataTables.js"></script>
        <script src="./js/jquery.uniform.js" type="text/javascript"></script>
        <script type="text/javascript" language="javascript" src="./js/jquery.jeditable.js"></script>

        <script type="text/javascript" charset="utf-8">
            var oTable;
            var iSelected = String();
            function delcookie()
            {
                var tmp_date=new Date()-10;
                document.cookie="TWX_member_zone=;expires=Thu, 01-Jan-70 00:00:01 GMT;";
                document.cookie="PHPSESSID=;expires=Thu, 01-Jan-70 00:00:01 GMT;";
                window.location.href="/?loggedout";

            }
            function getCookie(c_name)
            {
                if (document.cookie.length>0)
                {
                    c_start=document.cookie.indexOf(c_name + "=");
                    if (c_start!=-1)
                    {
                        c_start=c_start + c_name.length+1;
                        c_end=document.cookie.indexOf(";",c_start);
                        if (c_end==-1) c_end=document.cookie.length;
                        return unescape(document.cookie.substring(c_start,c_end));
                    }
                }
                return "";
            }



            $(function() {

                $("#tabs1").tabs();
                $.fn.clearForm = function() {
                    return this.each(function() {
                        var type = this.type, tag = this.tagName.toLowerCase();
                        if (tag == 'form')
                            return $(':input',this).clearForm();
                        if (type == 'text' || type == 'password' || tag == 'textarea')
                            this.value = '';
                        else if (tag == 'select')
                            this.selectedIndex = 0;
                    });

                };
                $( "#tabs1" ).tabs({
                    select: function(event, ui) {
                        $('#cronset').attr('style', 'display:none');
                        $('.feedsCron').attr('style', 'display:none');
                        $('.feeds').attr('style', 'display:block');
                        $('#croncheck').attr('checked', false);
                        $('#croncheck2').attr('checked', false);
                        $('#croncheck3').attr('checked', false);
                    }
                });

                $( "#addTaskDialog" ).dialog({
                    resizable: false,
                    height:650,
                    width:630,
                    modal: true,
                    autoOpen: false,
                    close: function() {
                        $( "#dialog-propagate" ).dialog( "close" );
                    },
                    buttons: {
                        "Add": function() {
                            $( this ).dialog( "close" );
                            var list = "act=add&type=task&" + $('form').serialize();
			    //document.write(list);
                            $.post("./includes/actions.php",  list,
                            function(data){
                                $("#showMessage").html(data);
                                location.reload();
                                $("#showMessage").css({display: "block"});
                                setTimeout(function(){
                                    $('#showMessage').fadeOut('slow', function() {
				      
                                    });

                                },3000);
			
                            });

                        },
                        Cancel: function() {
                            $("#fnSel").attr('value', '');
                            $( "#dialog-propagate" ).dialog( "close" );
                            $( this ).dialog( "close" );
                            
                        }
                    }
                });
                $( "#dialog-edit" ).dialog({
                    resizable: false,
                    height:650,
                    width:630,
                    modal: true,
                    autoOpen: false,
                    buttons: {
                        "Edit task": function() {
                            $( this ).dialog( "close" );
                            var sData = $("#editTask").serializeArray();
                            $.post("./includes/actions.php", { data: sData, act: "save", type: "task"},
                            function(data){
                                $("#showMessage").html(data);
                                //oTable.fnDraw();
                                $("#showMessage").css({display: "block"});
                                setTimeout(function(){
                                    $('#showMessage').fadeOut('slow', function() {
                                    });

                                },30000);

                            });

                        },
                        Cancel: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
                $( "#dialog-confirm" ).dialog({
                    resizable: false,
                    height:100,
                    width:360,
                    modal: true,
                    autoOpen: false,
                    buttons: {
                        "Delete task": function() {
                            var an = $("#fnSel").attr('value');
                            $( this ).dialog( "close" );
                            $.post("./includes/actions.php", { data: an, act: "delete", type: "task"},
                            function(data){
                                $("#showMessage").html(data);
                                location.reload();
                                $("#showMessage").css({display: "block"});
                                setTimeout(function(){
                                    $('#showMessage').fadeOut('slow', function() {
                                        // Animation complete
                                    });

                                },2000);

                            });

                        },
                        Cancel: function() {
                            $("#fnSel").attr('value', '');
                            $( this ).dialog( "close" );
                        }
                    }
                });
                $( "#dialog-propagate" ).dialog({
                    resizable: false,
                    height:400,
                    width:560,
                    modal: false,
                    autoOpen: false,
                    buttons: {
                        "Get lines": function() {
                            var an = $("#multval").attr('value');
                            $( this ).dialog( "close" );
                            $.post("./includes/actions.php", { data: an, act: "multiple"},
                            function(data){
                                $("#ins").val(data);
                            });
                        },
                        Cancel: function() {
                            $("#fnSel").attr('value', '');
                            $( this ).dialog( "close" );
                        }
                    }
                });

                $( "#DeleteTask" ).click(function() {
                    $( "#dialog-confirm" ).dialog( "open" );
                    return false;
                });
                $( "#propagate" ).click(function() {
                    $( "#dialog-propagate" ).dialog( "open" );
                    return false;
                });

                // clear all fields after request
                $('.add').click(function() {
                    $( "#addTaskDialog" ).dialog( "open" );
                    return false;
                });
                $( ".remove" ).click(function() {
                    var sData = $("#taskform").serialize();
                    $.post("./includes/actions.php", { data: sData, act: "deleteMass", type: "task"},
                    function(data){
                        $("#showMessage").html(data);
                        location.reload();
                        $("#showMessage").css({display: "block"});
                        setTimeout(function(){
                            $('#showMessage').fadeOut('slow', function() {
                                // Animation complete
                            });

                        },2000);

                    });

                    return false;
                });

                $( ".cron input" ).click(function() {
                    if ( $(this).hasClass('selected') ){
                        $(this).removeClass('selected');
                        $('#cronset').attr('style', 'display:none');
                        $('.feedsCron').attr('style', 'display:none');
                        $('.feeds').attr('style', 'display:block');
                        $('.feedsCron').attr('name', '');
                        $('.feeds').attr('name', 'feeds');
                        $('#tabs-2 small').text('(one url per line)');
                    }

                    else	{
                        $(this).addClass('selected');
                        $('#cronset').attr('style', 'display:block');
                        $('.feedsCron').attr('style', 'display:block');
                        $('.feeds').attr('style', 'display:none');
                        $('.feedsCron').attr('name', 'feeds');
                        $('.feeds').attr('name', '');
                        $('#tabs-2 small').text('(only one feed allowed in cron mode)');

                    }

                });
                $.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
                    if(oSettings.oFeatures.bServerSide === false){
                        var before = oSettings._iDisplayStart;
                        oSettings.oApi._fnReDraw(oSettings);
                        //iDisplayStart has been reset to zero - so lets change it back
                        oSettings._iDisplayStart = before;
                        oSettings.oApi._fnCalculateEnd(oSettings);
                    }
                    oSettings.oApi._fnDraw(oSettings);
                };


                $(document).ready(function() {

                    oTable = $('#example').dataTable( {
                        "bProcessing": true,
                        "bServerSide": true,
                        "sAjaxSource": "./includes/dataJSON.php?table=tasks",
                        "bFilter": false,
                        "iDisplayLength": 50,
                        "bStateSave": true,
                        "sPaginationType": "full_numbers",
                        "sDom": '<"top"i<"sAll">p>rt<"bottom"flp>',
                        "aaSorting": [[ 0, "desc" ]],
                        "aoColumns": [
                            { "sWidth": "40px"},
                            { "sWidth": "200px"},
                            { "sWidth": "30px", "bVisible": false },
                            { "sWidth": "40px"},
                            { "sWidth": "60px", "bVisible": false },
                            { "sWidth": "250px"},
                            { "sWidth": "60px", "bVisible": false },
                            { "sWidth": "70px"}
                        ],

                        "fnDrawCallback": function() {}

                    } );

                    $('.delete').live('click',function () {
                        $("#fnSel").attr('value', $(this).attr('id'));
                        $( ".ui-dialog-title" ).text('Delete task ' + $(this).attr('id'));
                        $( "#dialog-confirm" ).dialog( "open" );
                        return false;
                    });
                    $('.edit').live('click',function () {
                        var eId = $(this).attr('id').replace("e","");

                        $('#editId').attr('value', eId);
                        $.post("./includes/actions.php", {id:eId, act: "edit", type: "task"},
                        function(data){
                            $('#dialog-edit').html(data);
                        });
                        $( "#dialog-edit" ).dialog( "open" );
                        return false;
                    });
                    $('.run').live('click',function () {
                        var rId = $(this).attr('id').replace("r","");
                        var cookie = getCookie('TWX_member_zone');
			
                        $.post("./includes/run.php", {id:rId, cookie:cookie, act: "start"},
                        function(data){
                            $("#showMessage").html(data);
                            oTable.fnDraw();
                            $("#showMessage").css({display: "block"});
                            setTimeout(function(){
                                $('#showMessage').fadeOut('slow', function() {
                                });

                            },3000);
                        });
                        return false;

                    });
                    $('.stop').live('click',function () {
                        var rId = $(this).attr('id').replace("s","");
                        var cookie = getCookie('TWX_member_zone');
			
                        $.post("./includes/run.php", {id:rId, cookie:cookie, act: "stop"},
                        function(data){
                            $("#showMessage").html(data);
                            oTable.fnDraw();
                            $("#showMessage").css({display: "block"});
                            setTimeout(function(){
                                $('#showMessage').fadeOut('slow', function() {
                                });

                            },3000);
                        });
                        return false;
                    });

                    $('#selectAll').live('click',function () {
                        $(':checkbox').attr('checked', 'checked');
                    });
                    $('#deselectAll').live('click',function () {
                        $(':checkbox').attr('checked', '');
                    });

                    $('.sAll').html('<a href="javascript:void(NULL);" id="selectAll">selectAll</a> &nbsp; /' +
                        '<a href="javascript:void(NULL);" id="deselectAll">deselectAll</a>');

                } );
                $('.startall').click(function() {
                    var sData = $("#taskform").serialize();
                    $.post("./includes/actions.php", {id:sData, act: "startAll"},
                    function(data){
                        $("#showMessage").html(data);
                        oTable.fnDraw();
                        $("#showMessage").css({display: "block"});
                        setTimeout(function(){
                            $('#showMessage').fadeOut('slow', function() {
                            });

                        },3000);
                    });
                });
                $('.break').click(function() {
                    var sData = $("#taskform").serialize();
                    $.post("./includes/actions.php", {id:sData, act: "stopAll"},
                    function(data){
                        $("#showMessage").html(data);
                        oTable.fnDraw();
                        $("#showMessage").css({display: "block"});
                        setTimeout(function(){
                            $('#showMessage').fadeOut('slow', function() {
                            });

                        },3000);
                    });
                });

            });

            var refreshId = setInterval(function()
            {
                oTable.fnStandingRedraw();
            }, <?php echo $data[2]['opt_value']; ?> * 1000);
            


	    function insertHere(char)
	    {
		var area = document.getElementById("multval");
		if ((area.selectionStart)||(area.selectionStart=='0'))
		{ 
		  var p_start=area.selectionStart;
		  var p_end=area.selectionEnd;
		  area.value=area.value.substring(0,p_start)+char+area.value.substring(p_end,area.value.length);
		  area.selectionStart = p_start+1;
		  area.selectionEnd = p_end+1;
		  area.focus();
		}
	    }

            

        </script>

    </head>
    <body id="indexBody">


        <span style="color: #735184;    display: block;    font-weight: bold;    left: 10px;    position: absolute;    text-align: center;    top: 1px;    width: 90%;">
            <?php echo $message; ?></span>
        <div id="showMessage"></div>
        <!--edit window-->
        <div id="dialog-edit" title="Task editing">
            <div id="InnerEdit"></div>
        </div>


        <!--add task window-->

        <div id="addTaskDialog" title="Add new task">

            <form id="addTask">
                <div id="tabs1">
                    <label>Task name: </label> <input type="text" name="tw_name" value="" style="width:90%;" /><br/><br/><br/>
                    <ul id="navMenu">
                        <li><a href="#tabs-1">Add list of tweets</a></li>
                        <li><a href="#tabs-2">Add feeds</a></li>
                        <li><a href="#tabs-3">Follow</a></li>
                        <li><a href="#tabs-4">Retweet</a></li>
                    </ul>

                    <div id="tabs-1">

                        <label>Paste tweets:</label>
                        <p style="float:right;margin-right:25px;">
                            <a id="file-uploader" style="background: url('./i/upload.png') no-repeat;display: block;float: left;height: 35px;text-decoration: none;width: 35px;">

                                <noscript>
                                    <p>Please enable JavaScript to use file uploader.</p>
                                    <!-- or put a simple form for upload here -->
                                </noscript>
                            </a>

                            <script src="./js/fileuploader.js" type="text/javascript"></script>
                            <style>
                                .qq-uploader{
                                    text-indent:-9999px;
                                }
                                .qq-upload-button{
                                    height:30px;
                                }
                            </style>
                            <script>
                                function createUploader(){
                                    var uploader = new qq.FileUploader({
                                        element: document.getElementById('file-uploader'),
                                        action: './up.php',
                                        debug: true,
                                        onComplete: function(id, fileName, responseJSON){
                                            $("#ins").attr('disabled', 'disabled');
                                            $("#ins").text('Lines will be inserted from ' + fileName);
                                            $("#file-uploader").html("<input type='hidden' name='filename' value='"+ fileName +"' />");
                                        }
                                    });
                                }

                                // in your app create uploader as soon as the DOM is ready
                                // don't wait for the window to load
                                window.onload = createUploader;
                            </script>

                            <a href="#" id="propagate"><img src="/i/brackets.png" alt="multiple values" title="multiple values" /></a>
                        </p><br />
                        <textarea name="tweets" id="ins"></textarea><br/>
                        <small>(one tweet per line)</small></div>
                    <div id="tabs-2">

                        <span class="cron">
                            <b>Schedule this task?</b>
                            <input type="checkbox" name="cronned" id="croncheck" />
                        </span>
                        <label>Paste feeds:</label><br /><textarea name="feeds" class="feeds"></textarea>
                        <input class="feedsCron" />
                        <small>(one url per line)</small>			

                    </div>
                    <div id="tabs-3">

                        <span class="cron">
                            <b>Schedule this task?</b>
                            <input type="checkbox" name="cronned" id="croncheck2" />
                        </span>
                        <label>Whom to follow?:</label><br/><input type="text" name="follow" /><br />
                        <small><b>someman</b>, without @</small> <b>One account to follow for a task only!</b><br/>	
                    </div>
                    <div id="tabs-4">

                        <span class="cron">
                            <b>Schedule this task?</b>
                            <input type="checkbox" name="cronned" id="croncheck3" />
                        </span>
                        <label>What to retweet?:</label><br/><input type="text" name="retweet" style="width:400px"/><br />
                        <small>status url like http://twitter.com/someacc/status/14969909547678552064</small><br/>			
                    </div>

                </div>

                <fieldset id="cronset">
                    <legend align="center">Cron</legend>
                    <label>Launch every:</label><select name="cronIntval">
                        <option value="0" selected>none</option>
                        <option value="30" >30 minutes</option>
                        <option value="60" >1 hour</option>
                        <option value="180" >3 hours</option>
                        <option value="360" >6 hours</option>
                        <option value="720" >12 hours</option>
                        <option value="1440" >once a day</option>
                    </select>
                </fieldset>
                <div class="buttons"><br />
                    <label>Use num. of accounts:</label> <input type="text" size="5" value="0" name="numaccs" id="accs" /><small>(0 - use all, max - <?php echo $splash['accs']; ?>)</small><br />
                    <br />
                    <label><input type="radio" name="radio" value="order" /> Choose in sequence</label>
                    <label><input type="radio" name="radio" value="random" checked="checked" /> Choose randomly</label>
                    <br /><label>Select shortener:</label><select name="shortener">
                        <?php echo $list; ?>
                    </select>
                </div>

            </form>

        </div>



        <!--add task window-->

        <div id="dialog-confirm" title="" style="display:none;">
            <p class="warning"> This task will be
                permanently deleted and cannot be restored. Are you sure?</p>
            <form method="get" action="#">
                <input type="hidden" id="fnSel" value="" />
            </form>
        </div>
        <!--propagate window-->
        <div id="dialog-propagate" title="Multiplicate values" style="display:none;">
            <small>Example: I {add|post} {some|any} {cool| very cool| holy shit} {tweet|mark|text}</small>
	    <p>
	      <b> 
		<a href="#" style="margin:3px; font-size:14px; text-decoration:none; display:block; height:20px; width:20px; border:1px solid #eee; float:left; text-align:center;" onClick=insertHere("\{\}");>{}</a> &nbsp; &nbsp; &nbsp;
		<a href="#" style="margin:3px; font-size:14px; text-decoration:none; display:block; height:20px; width:20px; border:1px solid #eee; float:left; text-align:center;" onClick=insertHere("|");>|</a> 
	      </b>
	    </p>
	    <br />
            <textarea id="multval"></textarea>
        </div>

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
                                                                <span>Tasks:<abbr title="Total"><?php echo $splash['task']; ?></abbr> / <abbr title="Currently running"><?php echo $splash['task_active']; ?></abbr></span>
                                                                <span>Proxy:<?php echo $splash['proxy']; ?></span>
                                                                <span>Accounts:<?php echo $splash['accs']; ?></span>
                                                            </div>
                                                        </div>


                                                        <div class="row">
                                                            <div id="globalNav">
                                                                <ul>
                                                                    <li class="selected">
                                                                        <a href="./tasks.php">Tasks<span></span></a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="./accounts.php">Accounts<span></span></a>
                                                                    </li>
                                                                    <!--<li>
                                                                        <a href="./drips.php">Drips<span></span></a>
                                                                    </li>-->
                                                                    <li>
                                                                        <a href="./setts.php">Settings<span></span></a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="./about.php">About<span></span></a>
                                                                    </li>
                                                                </ul>
                                                                <span class="logout">
                                                                    <a href="javascript:delcookie();" title="Log out">Log out <img src="./i/enter.gif" alt="Log out" /></a>
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="wrapper">
                                                            <div id="nav-links">

                                                                <a class="add" title="Add new task" href="#">add</a>
                                                                <a class="remove" href="#" title="remove selected">remove</a>
                                                                <a class="startall" href="#" title="run selected tasks">run</a>
                                                                <a class="break" href="#" title="stop selected tasks">stop</a>
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


                                                        <div id="dt_example">
                                                            <form method="get" action="./test.html" id="taskform"/>
                                                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                                                <thead>

                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>TASK</th>
                                                                        <th>TYPE</th>
                                                                        <th>ACCS</th>
                                                                        <th>METHOD</th>
                                                                        <th>PROGRESS</th>
                                                                        <th>STATUS</th>
                                                                        <th>ACTIONS</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="8" class="dataTables_empty">Loading data from
			server</td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>

                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>TASK</th>
                                                                        <th>TYPE</th>
                                                                        <th>ACCS</th>
                                                                        <th>METHOD</th>
                                                                        <th>PROGRESS</th>
                                                                        <th>STATUS</th>
                                                                        <th>ACTIONS</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>

                                                            </form>
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

		Copyright © 2010 &nbsp;&nbsp;
                    <a href="http://twindexator.com" target="_blank"> Twindexator</a> &nbsp;&nbsp; | &nbsp;&nbsp;
                    <a href="http://forum.twindexator.com" target="_blank">Forum</a>&nbsp;&nbsp; | &nbsp;&nbsp;
                    <a href="mailto:support@twindexator.com" target="_blank">support@twindexator.com</a>
                </div>
            </div>
        </div>
    </body>
</html>
