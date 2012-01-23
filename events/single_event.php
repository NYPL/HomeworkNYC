<?php
include("../_db.php"); 
include("../_core.php"); 
include("../_platform.php"); 

$hash_id = isset($_REQUEST['hash_id']) ? $_REQUEST['hash_id'] : '0';
//$format = isset($_REQUEST['format'])?$_REQUEST['format']:'html';//I would LOVE to offer iCal formats

$events = doSelect(
	'event_cache', 
	'title,body,link,boro,audience,dateline,location,hash_id', 
	"WHERE hash_id = '{$hash_id}'");

	$event = mysql_fetch_object($events);
	
?>
<html>
	<head>
		<title><?=$event->title;?></title>
		<meta name="title" content="<?=$event->title;?>" /> 
		<meta name="description" content="<?=$event->body;?>" /> 
		<link rel="image_src" href="/widgets/media/hwNYC_thumb.gif" / >
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<?=js()?>
		<?=css()?>
		<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="no title" charset="utf-8">
	</head>
	<body>
		<div id="scroller">
		    <?php include('../_widget.header.php');?>
			<div align="center">
				<strong id="logotype">HomeWorkNYC: Events</strong><br />
			</div>
			<?php
				if(mysql_num_rows($events) == 0 ) {
					include('_event.no_result.tpl');
				} else {
					include('_event.brief.tpl');
				}
			?>
			<?php
			 if($event->link != ''):?>
				<div class="library_link">
				Library link<a target="_new" href="<?=$event->link?>"> <?=$event->link?></a>
				</div>
			<?php endif;?>
		</div>
		
	</body>
	
</html>