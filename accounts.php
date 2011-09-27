<?php
include_once './includes/info.php';
$info = new GetInfo('includes/');

$splash = $info->GetSplash();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Twindexator - Accounts</title>
        <link rel="stylesheet" type="text/css" href="./css/index.css" media="all" />
        <link rel="stylesheet" href="./css/smoothness/jquery-ui-1.8.4.custom.css" type="text/css"
              media="screen" />
        <link type="text/css" rel="stylesheet" href="./css/uniform.default.css" />
	<link REL="SHORTCUT ICON" HREF="./favicon.ico">
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
                window.location.href="http://clients.twindexator.com/?loggedout";

            }

            $.fn.dataTableExt.oApi.fnLengthChange = function ( oSettings, iDisplay )
            {
                oSettings._iDisplayLength = iDisplay;
                oSettings.oApi._fnCalculateEnd( oSettings );

                /* If we have space to show extra rows (backing up from the end point - then do so */
                if ( oSettings._iDisplayEnd == oSettings.aiDisplay.length )
                {
                    oSettings._iDisplayStart = oSettings._iDisplayEnd - oSettings._iDisplayLength;
                    if ( oSettings._iDisplayStart < 0 )
                    {
                        oSettings._iDisplayStart = 0;
                    }
                }

                if ( oSettings._iDisplayLength == -1 )
                {
                    oSettings._iDisplayStart = 0;
                }

                oSettings.oApi._fnDraw( oSettings );

                $('select', oSettings.oFeatures.l).val( iDisplay );
            }

            $(function() {



                $("select").uniform();


                $( "#add-accs-dialog" ).dialog({
                    resizable: false,
                    height:550,
                    width:630,
                    modal: true,
                    autoOpen: false,
                    buttons: {
                        "Add": function() {
                            $( this ).dialog( "close" );
                            var list = $('textarea#accList').val();
                            $.post("./includes/actions.php", { data: list, act: "add", type: "accs"},
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
                        "Delete accounts": function() {
                            var an = $("#fnSel").attr('value'); 
                            $( this ).dialog( "close" ); 
                            $.post("./includes/actions.php", { data: an, act: "delete", type: "accs"},
                            function(data){
                                $("#showMessage").html(data);
                                location.reload();
                                $("#showMessage").css({display: "block"});
                                setTimeout(function(){
                                    $('#showMessage').fadeOut('slow', function() {
                                        // Animation complete
                                    });

                                },20000);

                            });

                        },
                        Cancel: function() {
                            $("#fnSel").attr('value', '');
                            $( this ).dialog( "close" );
                        }
                    }
                });

                // clear all fields after request
                $('.add').click(function() { 
                    $( "#add-accs-dialog" ).dialog( "open" );
                    return false;
                });
                $( ".remove" ).click(function() {
                    $( ".ui-dialog-title" ).text('Delete selected accounts?');
                    $( "#dialog-confirm p").text('You\'re going to delete selected accounts.');
                    $( "#dialog-confirm" ).dialog( "open" );
                    return false;
                });
                $( ".delete" ).click(function() {
                    $("#fnSel").attr('value', "-1");
                    $( ".ui-dialog-title" ).text('Delete all accounts in database?');
                    $( "#dialog-confirm p").text('This will delete at once all accounts you have.');   
                    $( "#dialog-confirm" ).dialog( "open" );
                    return false;
                });

                $(document).ready(function() {


                    oTable = $('#example').dataTable( {
                        "bProcessing": true,
                        "bServerSide": true,
                        "sAjaxSource": "./includes/dataJSON.php?table=accounts",
                        "bAutoWidth": true,
                        "bShowFilter": false,
                        "aLengthMenu": [[50, 100, 500, -1], [50, 100, 500, 'All']],
                        "sDom": '<"top"<"selects">i<"sAll">p>rt<"bottom"ip><"clear">',
                        "sPaginationType": "full_numbers",
                        "iDisplayLength": 50,
                        "aoColumns": [
                            { "sWidth": "10px" },
                            { "sWidth": "280px" },
                            { "sWidth": "280px" },
                            { "sWidth": "20px" }
                        ],
                        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                            $(nRow).children(':nth-child(2)').addClass("editable")
                            $(nRow).children(':nth-child(2)').attr("title", "click to edit")
                            return nRow;
                        },

                        "fnDrawCallback": function() {
                            $('.editable', this.fnGetNodes()).editable('./includes/actions.php', {
                                "callback": function( sValue, y ) {
                                    var aPos = oTable.fnGetPosition( this );
                                    oTable.fnUpdate( sValue, aPos[0], aPos[1] );
                                },

                                "submitdata": function (value,settings ) {
                                    var aPos = oTable.fnGetPosition(this);
                                    var aData = oTable.fnSettings().aoData[aPos[0]]._aData;
                                    return {"recId":aData[0],"act":"update", "type" : "accs"};
                                },

                                "height": "14px"
                            } );

                        }


                    } );

                    $("#myFilter").bind("keyup", function(event) {
                        oTable.fnFilter($(this).val().trim());
                    });

                    $('#example tbody tr ').live('click', function () {
                        var aData = oTable.fnGetData( this );
                        var an = $("#fnSel").attr('value');
                        if ( $(this).hasClass('row_selected') ){
                            $(this).removeClass('row_selected');
                            $("#fnSel").attr('value', an.replace("|" + aData[0], ""));
                        }

                        else	{
                            $(this).addClass('row_selected');
                            $("#fnSel").attr('value', an + "|" + aData[0]);
                        }

                    } );



                    $('#selectAll').live('click',function () {
                        fnGetSelected(oTable);
                    });
                    $('#deselectAll').live('click',function () {
                        fnGetDeSelected(oTable);
                    });

                    $('.delete').live('click',function () { 
                        $("#fnSel").attr('value', $(this).attr('id'));
                        $( ".ui-dialog-title" ).text('Delete account ' + $(this).attr('id') + ' from database?');
                        $( "#dialog-confirm" ).dialog( "open" );
                        return false;
                    });

                } );

                $('#my_length').change( function() {
                    var lengthVal = $('#my_length option:selected').val();
                    oTable.fnLengthChange(lengthVal);
                } );



                function fnGetSelected( oTableLocal )
                {
                    var aReturn = new Array();
                    var aTrs = oTableLocal.fnGetNodes();
                    for ( var i=0 ; i<aTrs.length; i++ )
                    {
                        var an = $("#fnSel").attr('value');
                        var aData = oTableLocal.fnGetData( aTrs[i]);
                        $("#fnSel").attr('value', an + "|" + aData[0]);
                        if ( $(aTrs[i]).hasClass('row_selected') )
                        {
                            aReturn.push( aTrs[i] );
                        } else {
                            $(aTrs[i]).addClass('row_selected');
                        }
                    }
                    return aReturn;
                }
                function fnGetDeSelected( oTableLocal )
                {
                    var aReturn = new Array();
                    var aTrs = oTableLocal.fnGetNodes();
                    $("#fnSel").attr('value', '');
                    for ( var i=0 ; i<aTrs.length; i++ )
                    {
                        if ( $(aTrs[i]).hasClass('row_selected') )
                        {
                            aReturn.push( aTrs[i] );
                            $(aTrs[i]).removeClass('row_selected');
                        }
                    }
                    return aReturn;
                }




                $('.selects').append($('.lForm'));
                $('.sAll').html('<a href="javascript:void(NULL);" id="selectAll">selectAll</a> &nbsp; /' +
                    '<a href="javascript:void(NULL);" id="deselectAll">deselectAll</a>');

            });

        </script>

    </head>
    <body id="indexBody">
        <div id="showMessage" style="display: none;"></div>


        <div id="add-accs-dialog" title="Add accounts">
            <form>
                <p style="float: left;">Paste: <small>(separate with <strong>: colon</strong>)</small></p>
                <textarea name="accs" style="width: 99%; height: 400px;" rows="20" cols="20" id="accList"></textarea>
            </form>
        </div>

        <div id="dialog-confirm" title="">
            <p class="warning"> This accounts will be
                permanently deleted and cannot be restored. Are you sure?</p>
            <form method="get" action="#">
                <input type="hidden" id="fnSel" value="" />
            </form>
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
                                                                    <li class="selected">
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
                                                            <div class="search">
                                                                <form action="post">
                                                                    <span>
                                                                        <input type="text" value="Search" id="myFilter" onfocus="if(this.value =='Search' ) this.value=''" onblur="if(this.value=='') this.value='Search'" class="input1" value="Search" />
                                                                        <input type="image" title=" Search " alt="Search" src="./i/search.gif" />
                                                                    </span>

                                                                </form>
                                                            </div>


                                                            <div id="selectable" >
                                                                <label>Select error :</label>
                                                                <select id="select_box" ONCHANGE="oTable.fnFilter(this.options[this.selectedIndex].value);">
                                                                    <option selected>&nbsp;</option>
                                                                    <option value="suspend">suspended</option>
                                                                    <option value="not exist">user not exist</option>
                                                                    <option value="not correct">wrong login or pass</option>
                                                                    <option value="">clear</option>
                                                                </select>
                                                            </div>

                                                            <div id="nav-links">

                                                                <a class="add" href="#" title="add new accounts">add</a>
                                                                <a class="remove" href="#" title="remove selected">remove</a>
                                                                
                                                                <a class="export" href="./includes/actions.php?act=export&amp;what=accs" target="_blank" title="get accounts as a list">export</a>
								<p style="margin:0 10px; padding:0; float:left;">&nbsp;</p>
								<a class="delete" href="#" title="!delete all accounts in database!">delete all</a>
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


                                                        <span class="lForm">
                                                            <label>Show</label>
                                                            <select name="my_length" id="my_length">
                                                                <option value="50" selected>50</option>
                                                                <option value="100">100</option>
                                                                <option value="500">500</option>
                                                                <option value="-1">All</option>
                                                            </select> </span>



                                                        <div id="dt_example">
                                                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                                                <thead>

                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>PAIR</th>
                                                                        <th>LAST ERROR</th>
                                                                        <th>ACTIONS</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="4" class="dataTables_empty">Loading data from
			server</td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>

                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>PAIR</th>
                                                                        <th>LAST ERROR</th>
                                                                        <th>ACTIONS</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
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