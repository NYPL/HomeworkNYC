<?php
include("../_platform.php");
include("../_db.php");
include("../_core.php");
include("../Mobile_Detect.php");

/* 	instead of creating a tiny file that includes the core bits and then calls this function. 
 	I just include it here. script only calls itself.
*/
if(isset($_REQUEST['action'])){
	if ($_REQUEST['action'] == 'ajax_search') {
	    if ($_REQUEST['is_onload'] == '0')
			match_feed_items('action');
		else
		   match_feed_items('cookie');
	}
}

if($platform == 'facebook') {
	//include_once $_SERVER['DOCUMENT_ROOT'].'/widgets/lib/facebook/facebook.php';
	//$facebook = new Facebook('4590ba4a20b0eb65a48f1e6d46db21ae', '61e2b789fd3aec74b62561943d3f9ff1');
	//$facebook->require_frame();
	//$nypl_user_id = $facebook->require_login();
}

require_once('_feeds.php');

function autocomplete_events() {
	/* Notice this function ends with DIE()
	it is another stub that is here as a convenience instead of creating small files and urls for simple features
	returns simple list of words that match the autocomplete stub passed in (separated by newline )
	*/
	$stub = $_GET["q"];
	$terms = '';
	$results = doQuery("SELECT DISTINCT title FROM event_cache WHERE title LIKE '%{$stub}%' ORDER BY title");
	while ($row = mysql_fetch_object($results)):
		$terms .= $row->title."\n";
	endwhile;
	die($terms);
}

function match_feed_items($type) {
	$page = ($_REQUEST['page'])?$_REQUEST['page']:1;
	$items_per_page = 10;
	$row_start = $items_per_page*($page-1);

	$sql_additional_clauses = '';
	$are_there_boros = '';
	$match_boros = array();
	
	if($type=='action' && isset($_REQUEST['searchterm']) && $_REQUEST['searchterm']!="")
	{
		if(isset($_REQUEST['ddl_events']) && $_REQUEST['ddl_events']!='all') {
			$are_there_boros = ' AND ';// used by STRING Match to "AND" them together
			$boro=$_REQUEST['ddl_events'];
			$match_boros[] = "boro = '{$boro}'";
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
	}
	else
	{
	   if(isset($_REQUEST['ddl_events']) && $_REQUEST['ddl_events']!='all') 
	   {
	      $boro=$_REQUEST['ddl_events'];
		 
		  $sql_additional_clauses = "boro = '{$boro}' and";
	   }
	   $sql_additional_clauses .= "  dateline>CURDATE() ORDER BY dateline";
	  
	   $events = doSelect(
			'event_cache',
			'title,body,link,boro,audience,dateline,location,hash_id ',
			"where {$sql_additional_clauses} LIMIT 30");
	}
	
	$num_rows = mysql_num_rows($events);
	if($num_rows == 0 ) {
		include('_event.no_result.tpl');
	} else {
		//paginate($num_rows, $page,$items_per_page);
		mysql_data_seek($events, $row_start);
		$i=0;
		while($event = mysql_fetch_object($events)) {
		
			include('_event.brief.tpl');
			if($i++ > $items_per_page) {
				break;
			}
		}
		paginate($num_rows, $page, $items_per_page);
	}
	die();
}

?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>AttendIt - powered by HomeWorkNYC</title>
	<?php 
	
	$detect = new Mobile_Detect();
	$mobilePlatform = '';

	if ($detect->isMobile()) {
		$mobilePlatform = 'mobile';
		mobileResources();
	}
	
	
	?>
	
	<?=js()?>
	<?=css()?>
	<script type="text/javascript" charset="utf-8">
	    var cookie_options = { path: '/', expires: 100000 };
		
		window.onload = function () {
		    $('#is_onload').val('1');
			if($.cookie('homeworkNYC_event_boro')) {
				$("#ddl_events").val($.cookie('homeworkNYC_event_boro'));
			}
			$('#search_result').load('',   $("#searchEvents").serialize(), function() {
//					$('.instructions').html('Click on an event title to find out more.');
			});
		}
		
		$(document).ready(function() {
			$('.raw').click(function() {
				$(this).children('pre').slideToggle();
			})

			$('#searchEvents').submit(function() {
			    $('#is_onload').val('0');
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
			
           $('#ddl_events').change(function() {
		        $.cookie('homeworkNYC_event_boro', $('#ddl_events').val(), cookie_options)
				if($('#searchterm').val() != '') {
					$('#searchEvents').submit();
					console.log("search");
				}

				else {
					$('#search_result').load('', $("#searchEvents").serialize(), function() { console.log("switch");});
				}
			})
			
			
			// add slide animation to reveal share buttons
			$('.event').live ('click', function(){
				$(this).find('.event_detail_dd').slideToggle();
			});
			
			//allow share buttons to be clicked without slidetoggle
			$('.event .share-toolbox').live ('click', function(){
				e.stopPropagation();
			});
		});
	</script>
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<!-- <script type="text/javascript" src="../lib/jqtouch.js"></script> -->
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=homeworknyc"></script>
</head>
<body>
	<div id="scroller" class="<?=$platform?> <?=$mobilePlatform?>">
	    <?php include('../_widget.header.php');?>
		<form id="searchEvents" action="" method="post">
				<p id="event-instructions">Type a program topic.  Pick a borough. Click GO.</p>
				<!-- <strong id="logotype">AttendIt <span class="beta">[beta]</span></strong><br /> -->
				<table id="search-controls">
				<tr>
				<td style="width: 100%;"><input type="text" name="searchterm"  id="searchterm" /></td>
				<td width="width: 10px;">&nbsp;<input type="hidden" name="action" value="ajax_search" id="action" /><input type="hidden" id="is_onload" name="is_onload" value="0" /></td>
				<td><INPUT TYPE="image" SRC="../media/btn_go.png" HEIGHT="30" WIDTH="52" BORDER="0" ALT="Go &rarr;"></td>
				</tr>
				</table>
				<br />
				<div id="feed_ddl">
					<select name="ddl_events" id="ddl_events" >
						<option value="all">All</option>
						<? foreach($feeds as $feed):?>
							  <option value="<?=$feed['boro']?>" ><?=$feed['boro']?></option>
						<? endforeach;?>
					</select>
				</div>
				
				<div class="instructions" style="display: none;">
					Type in the name or type of event that you would like to attend.  If you would only like to find events in one or more boroughs, select that location.  Click "Go" or select an event that looks interesting from the drop-down list that appears.
				</div>
			<div id="autocomplete">

			</div>
			<div id="search_result">

			</div>
			<div id="powered-footer">
				<p>Powered by:</p>
				<p><img src="../media/nypl_logo.png" alt="NYPL" /><img src="../media/queenslibrary.png" alt="Queens Library" /><img src="../media/brooklynlibrary.png" alt="Brooklyn Library" /></p>
			</div>
			
		</form>
		<?php include('../_widget.footer.php');?>

	</div>
	<?php 
	/* This is the clever bit. 
	I load the _cache script into an object tag 
	so that populating the cache doesn't punish anyone's UX
	*/
	if($update_cache):?>
		<object data="_cache.php" style="display:none"></object>
	<?php endif;?>
	<div style="display: none;"><span style="color:black">cache last updated: <?=date('m/d/y h:m', $cache_last_updated)?></span></div>
</body>
</html>

