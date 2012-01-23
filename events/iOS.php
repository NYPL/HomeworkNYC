<?php
//iOS("../_platform.php"); 
include("../_db.php"); 
include("../_core.php"); 

if(isset($_REQUEST['action'])){
	if ($_REQUEST['action'] == 'ajax_search') {
		match_feed_items();
	} elseif($_REQUEST['action'] == 'autocomplete') {
		autocomplete_events();
	}
}

$platform = 'iOS';
require_once('_feeds.php');




/* 
check REQUEST to see if NEW filters are passed in
if true set local #checkboxfilters to _POST var
if false check cookie
if COOKIE is set, set LOCAL to COOKIE
either way when all is settled, set cookie for next time.
$checkboxfilters = array();
if(isset($_REQUEST['checkboxfilters'])) {
	$checkboxfilters = $_REQUEST['checkboxfilters'];
} else {
	if(isset($_COOKIE['HWNYC_checkboxfilters'])) {
		$checkboxfilters = explode('-',$_COOKIE['HWNYC_checkboxfilters']);
	}
}
setcookie('HWNYC_checkboxfilters', implode('-',$checkboxfilters));
*/
function autocomplete_events() {
	$stub = $_GET["q"];//$_REQUEST['searchstub'];
	$terms = '';
//	$results = doSelect('event_cache', 'title', "WHERE title LIKE '%{$stub}%' ORDER BY title" );
	$results = doQuery("SELECT DISTINCT title FROM event_cache WHERE title LIKE '%{$stub}%' ORDER BY title");
	while ($row = mysql_fetch_object($results)):
		$terms .= $row->title."\n";
	endwhile;
	die($terms);
}

function match_feed_items() {
	$page = ($_REQUEST['page'])?$_REQUEST['page']:1;
	$items_per_page = 30;
	$row_start = $items_per_page*($page-1);

	$sql_additional_clauses = '';
	$are_there_boros = '';
	$match_boros = array();
	if(isset($_REQUEST['boro'])) {
		$are_there_boros = ' AND ';// used by STRING Match to "AND" them together
		foreach($_REQUEST['boro'] as $b) {
			$match_boros[] = "boro = '{$b}'";
		}
	}
	if(count($match_boros) != 0) {
		$sql_additional_clauses .= '('.implode(' OR ', $match_boros).') ';
	}

	if(isset($_REQUEST['searchterm'])) {
		$searchterm = doEscape($_REQUEST['searchterm'], false);
		$match_phrase = "MATCH (title, body, boro, audience, location) AGAINST ('{$searchterm}' )";
		if($are_there_boros != '') {
			$sql_additional_clauses .= " {$are_there_boros} ({$match_phrase}) ";
		} else {
			$sql_additional_clauses .= " {$match_phrase} ";
		}

	}
	$sql_additional_clauses .= " ORDER BY relevance DESC";//", dateline DESC";
//	$sql_additional_clauses .= " LIMIT {$row_start}, {$items_per_page} ";

	$events = doSelect(
		'event_cache', 
		'title,body,link,boro,audience,dateline,location,hash_id,'.$match_phrase.' as relevance', 
		"WHERE {$sql_additional_clauses}");
	$num_rows = mysql_num_rows($events);
	if($num_rows == 0 ) {
		include('_event.no_result.tpl');
	} else {
//		paginate($num_rows, $page,$items_per_page);
		mysql_data_seek($events, $row_start);
		$i=0;
		while($event = mysql_fetch_object($events)) {
			include('_event.iOS.tpl');
			if($i++ > $items_per_page) {
				break;
			}
		}
//		paginate($num_rows, $page, $items_per_page);
	}
	die();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
    <meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="apple-touch-icon" href="apple-touch-icon.png" />
    <link rel="apple-touch-startup-image" href="iOS_startup320x460.png" />
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>AttendIt!</title>
	<?=js()?>
	
	<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
	<link rel="stylesheet" href="UiUIKit/stylesheets/iphone.css" />
	
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('.raw').click(function() {
				$(this).children('pre').slideToggle();
			})
			
			$('#searchEvents').submit(function() {
				$('#search_result').load('',  $("#searchEvents").serialize(), function() {
//					$('.instructions').html('Click on an event title to find out more.');
				});
				return false;
			}) 
			
			$('.page_nav').live('click', function() {
				if ( $(this).attr('id') == 'next' ) {
					page = parseInt($('#paginate #current_page').html()) + 1;
				} else {
					page = parseInt($('#paginate #current_page').html()) - 1;
				}
				q = $('#searchterm').val();
				$('#search_result').load('?page='+page+'',  $("#searchEvents").serialize(), function() {});
			})
			
			
		});
		function clickclear(thisfield, defaulttext) {
			if (thisfield.value == defaulttext) {
				thisfield.value = "";
			}
		}
	</script>
	<style type="text/css" media="screen">
		a {border:0;}
		
	</style>
</head>
<body>
	<div id="header">
		<h1 id="pageTitle">AttendIt!</h1>
        <a id="Button" onclick="$('#instructions').toggle();" class="Button" href="#">Instructions</a>
        <!-- <a class="button" href="#searchForm">Search</a> -->
    </div>
	<ul style="display:none" id="instructions" title="AttendIt! instructions">
		<li>Type in the name or type of event that you would like to attend.  If you would only like to find events in one or more boroughs, select that location.  Click "Go" or select an event that looks interesting from the drop-down list that appears.</li>
		<a href="#" onclick="$('#instructions').hide();" class="white button">Close</a>
	</ul>
	<form style="padding:0;margin:0" title="AttendIt!" id="searchEvents" class="dialog" action="" method="post">
		<ul class="form">
			<li><input type="text" name="searchterm" value="Search Events" id="searchterm" onclick="clickclear(this, 'Search Events')" onblur="clickrecall(this,'Search Events')" /></li>
			<input type="hidden" name="action" value="ajax_search" id="action" />
				<? foreach($feeds as $feed):?>
					<li><input type="checkbox" name="boro[]" value="<?=$feed['boro']?>" id="<?=$feed['boro']?>"> <label for="<?=$feed['boro']?>"><?=$feed['boro']?> [<?=$cached_counts[$feed['boro']]?$cached_counts[$feed['boro']]:'missing'?> events]</label> </li>
				<? endforeach;?>
			
		</ul>
		<p><a href="#" onclick="$('#searchEvents').submit();" class="button white" id="search_button"  value="Search">Search Events</a></p>
	</form>
	<ul id="search_result">
	</ul>
		
	<?php include('../_widget.footer.php');?>
	
</body>
</html>

