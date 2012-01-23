<?php
	/*
		TEMPLATE for events. It is included in a loop and dynamically populated. A simply method for extracting display code from programatic code.
	*/
	$striate = isset($striate)?$striate:0;
	$striate++;
?>
<div class="event color_<?=($striate % 2)?>" href="#blackbox">
	<div class="date_location">
		<div class="dateline">
			<?=date( 'D, M jS, Y', strtotime($event->dateline));?>
		</div>
		<div class="location">
			<?=$event->location;?>, <?=$event->boro;?>
		</div>
	</div>
	<div class="event_title_description">
		 <div class="event_title"><a href="single_event.php?hash_id=<?=$event->hash_id;?>"><?=$event->title;?></a></div>
		<div class="event_title"><?=$event->title;?></div>
		<?=$event->body;?>

	</div>

	<?php
		$url = "http://".$_SERVER['SERVER_NAME']."/widgets/events/single_event.php?hash_id={$event->hash_id}";
		$description = "{date( 'D, M jS, Y', strtotime($event->dateline))} {$event->location}, {$event->boro} {$event->body} ";
		//$share_data = array('url'=>$url, 'title'=>$event->title, 'description'=>$description);
	?>
	<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style ">
	<a class="addthis_button_preferred_1" addthis:url="<?=$url;?>" addthis:title="<?=$event->title;?>"></a>
	<a class="addthis_button_preferred_2" addthis:url="<?=$url;?>" addthis:title="<?=$event->title;?>"></a>
	<a class="addthis_button_preferred_3" addthis:url="<?=$url;?>" addthis:title="<?=$event->title;?>"></a>
	<a class="addthis_button_preferred_4" addthis:url="<?=$url;?>" addthis:title="<?=$event->title;?>"></a>
	<a class="addthis_button_compact" addthis:url="<?=$url;?>" addthis:title="<?=$event->title;?>"></a>
	<a class="addthis_counter addthis_bubble_style"></a>
	</div>

	<script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=homeworknyc"></script>
	<!-- AddThis Button END -->
	
</div>