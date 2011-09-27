<?php 
//error_reporting(0);
require_once('./info.php');
$info = new GetInfo('');
$gaSql = $info->gaSql;

$sIndexColumn = "id";
$sTable = "`cust_tables`.`".$info->pref."_".$_GET['table']."`";

$aColumns = array( 'id', 'task_name', 'source', 'used_accounts', 'ordering', 'progress', 'status' );
if($_GET['table'] !== 'tasks')
    $aColumns = array( 'id', 'pair', 'error' );



$gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
        die( 'Could not open connection to server' );

mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
        die( 'Could not select database '. $gaSql['db'] );


/* 
	 * Paging
*/
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
    $sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
            mysql_real_escape_string( $_GET['iDisplayLength'] );
}


/*
	 * Ordering
*/
if ( isset( $_GET['iSortCol_0'] ) ) {
    $sOrder = "ORDER BY  ";
    for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
        if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
            $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
        }
    }

    $sOrder = substr_replace( $sOrder, "", -2 );
    if ( $sOrder == "ORDER BY" ) {
        //$sOrder = "";
        $sOrder = "ORDER BY 1 DESC";
    }
}


/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
*/
$sWhere = "";
if ( $_GET['sSearch'] != "" ) {
    $sWhere = "WHERE (";
    for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
        $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
    }
    $sWhere = substr_replace( $sWhere, "", -3 );
    $sWhere .= ')';
}

/* Individual column filtering */
for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
    if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ) {
        if ( $sWhere == "" ) {
            $sWhere = "WHERE ";
        }
        else {
            $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
    }
}


/*
	 * SQL queries
	 * Get data to display
*/
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
        "; //echo $sQuery."\r\n";
$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());

/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS()
	";
$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
        ";
$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];


/*
	 * Output
*/
$sOutput = '{';
//$sOutput .= '"sEcho": '.intval($_GET['sEcho']).', ';
$sOutput .= '"sEcho": '.intval($_GET['sEcho']).', ';
$sOutput .= '"iTotalRecords": '.$iTotal.', ';
$sOutput .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
$sOutput .= '"aaData": [ ';
while ( $aRow = mysql_fetch_array( $rResult ) ) {
    $sOutput .= "[";
    for ( $i=0 ; $i<count($aColumns) ; $i++ ) {


        if ( $aColumns[$i] == "version" ) {
            /* Special output formatting for 'version' */
            $sOutput .= ($aRow[ $aColumns[$i] ]=="0") ?
                    '"-",' :
                    '"'.str_replace('"', '\"', $aRow[ $aColumns[$i] ]).'",';
        }
        else if ( $aColumns[$i] != ' ' ) {
            /* General output */
	    if($_GET['table'] == "tasks"){
    		$query = mysql_query("SELECT `ordering`, `cronIntval` FROM ".$sTable." WHERE `id` = ".$aRow[ $aColumns[0] ].";"); 
		$res = mysql_fetch_array($query);
	    
	    if($i == 0){

		switch($aRow[ $aColumns[$i + 2] ]){
		  case("tweets"):
		    $sOutput .= '"'.str_replace('"', '\"','<input type=\'checkbox\' name=\''. $aRow[ $aColumns[$i] ] . '\'/><span style=\'float:left\'>'. $aRow[ $aColumns[$i] ] . '</span><span title=\'Tweets\' class=\'tweetsicon\'>&nbsp;</span>').'",';
		    break;
		  case("feeds"):
		    $sOutput .= '"'.str_replace('"', '\"', '<input type=\'checkbox\' name=\''. $aRow[ $aColumns[$i] ] . '\'/><span style=\'float:left\'>'. $aRow[ $aColumns[$i] ] .  '</span><span title=\'Feeds\' class=\'feedsicon\'>&nbsp;</span>').'",';
		    break;
		  case("retweet"):
		    $sOutput .= '"'.str_replace('"', '\"', '<input type=\'checkbox\' name=\''. $aRow[ $aColumns[$i] ] . '\'/><span style=\'float:left\'>'. $aRow[ $aColumns[$i] ] . '</span><span title=\'Retweet\' class=\'retweeticon\'>&nbsp;</span>').'",';
		    break;
		  case("follow"):
		    $sOutput .= '"'.str_replace('"', '\"', '<input type=\'checkbox\' name=\''. $aRow[ $aColumns[$i] ] . '\'/><span style=\'float:left\'>'. $aRow[ $aColumns[$i] ] . '</span><span title=\'Follow\' class=\'followicon\'>&nbsp;</span>').'",';
		    break;
		}
		continue;
	    }

            if($i == 1):
		if(strlen($aRow[ $aColumns[$i] ]) < 1){
				  $aRow[ $aColumns[$i] ] = "noname task";}
		$ending = "";
		if(strlen($res['cronIntval']) > 1){
			    $ending = '<span class=\'cronicon\' title=\'cronned\' style=\'text-indent:-20px;\'>'.$res['cronIntval'].'</span>';
			} 
		    $sOutput .= '"'.str_replace('"', '\"', '<span style=\'float:left\'>'. $aRow[ $aColumns[$i] ] . '</span>' . $ending).'",';
                continue;
            endif;
	    if($i == 3): 
		if($aRow[ $aColumns[$i] ] == 0){
			    $aRow[ $aColumns[$i] ]= "all";}

		if($res['ordering'] == "order"){
			    $ending = "<span class=\"ordericon\" title=\"in a sequence\">&nbsp</span>";
			} else {
			    $ending = "<span class=\"randomicon\" title=\"random\">&nbsp</span>";
			}

		    $sOutput .= '"'.str_replace('"', '\"','<span style=\'float:left\'>'. $aRow[ $aColumns[$i] ] . '</span>' . $ending).'",';
	    continue;
	    endif;
            if($i == 5): $sOutput .= '"'.str_replace('"', '\"', '<div class=\'ui-progressbar ui-widget ui-widget-content ui-corner-all\'><div style=\'width: '.$aRow[ $aColumns[$i] ].'%;\' class=\'ui-progressbar-value ui-widget-header ui-corner-left\'></div><p class=\'text\'>'.$aRow[ $aColumns[$i] ]).'%</p></div>",';
                continue;
            endif;
            if($i == 6):
                if($aRow[ $aColumns[$i] ] == 'stop') {
                    $buttin = str_replace('', '', '" <a title=\'Start task\' class=\'run\' href=\'#\' id=\'r'.$aRow[ $aColumns[0] ].'\'><img src=\'./images/icons/start.png\' /></a>');
                } else {
                    $buttin = str_replace('', '', '" <a title=\'Stop task\' class=\'stop\' href=\'#\' id=\'s'.$aRow[ $aColumns[0] ].'\'><img src=\'./images/icons/pause.png\' /></a>');
            }
            //continue;
            endif;
	    }
            $sOutput .= '"'.str_replace('"', '\"', $aRow[ $aColumns[$i] ]).'",';

        }
    }

    /*
		 * Optional Configuration:
		 * If you need to add any extra columns (add/edit/delete etc) to the table, that aren't in the
		 * database - you can do it here
    */


    //$sOutput = substr_replace( $sOutput, "", -1 );
    //$sOutput .= '","';
    if($_GET['table'] != 'tasks') {
        $sOutput .=  $buttin  . str_replace('', '', '"&nbsp;&nbsp;&nbsp;&nbsp; <!--<a class=\'edit\' href=\'#\' id=\'e'.$aRow[ $aColumns[0] ]. '\'>&nbsp;</a>--> <a class=\'delete\' alt=\'delete\' title=\'delete\' href=\'#\' id=\''.$aRow[ $aColumns[0] ].'\'>&nbsp;</a>"');
    } else {
        $sOutput .=  $buttin  . str_replace('', '', ' <a title=\'Edit task\' class=\'edit\' href=\'#\' id=\'e'.$aRow[ $aColumns[0] ].'\'><img src=\'./images/icons/edit.png\' /></a> <a title=\'Delete task\' class=\'delete\' href=\'#\' id=\''.$aRow[ $aColumns[0] ].'\'><img src=\'./images/icons/stop.png\' /></a> <a title=\'View log\' class=\'view_log\' href=\'./tmp/'.$aRow[ $aColumns[0] ].'_'.$info->pref.'-links.txt\' target=\'_blank\'><img src=\'./images/icons/log.png\' /></a>"');
    }
    $sOutput .= "],";

}
$sOutput = substr_replace( $sOutput, "", -1 );
$sOutput .= '] }';

echo $sOutput;
?>
