<?php
	/*
		TEMPLATE for events. It is included in a loop and dynamically populated. A simply method for extracting display code from programatic code.
	*/
	$striate = isset($striate)?$striate:0;
	$striate++;
?>
<div class="event color_<?=($striate % 2)?> clearfix">
	
	<div class="event_title"><?=$event->title;?></div>
	<div class="dateline"><?=date( 'D, M jS, Y', strtotime($event->dateline));?></div>
	<div class="location"><?=$event->location;?>, <?=$event->boro;?></div>
	<div class="event_detail_dd clearfix">
	<div class="event_body"><?=$event->body;?></div>
	
	<!-- AddThis Button start -->
	<?php
		$url = 'http://'.$_SERVER['SERVER_NAME']."/widgets/events/single_event.php?hash_id={$event->hash_id}";
		//$url = "http://homeworkNYCbeta.org/widgets/events/single_event.php?hash_id={$event->hash_id}";
		$description = "{date( 'D, M jS, Y', strtotime($event->dateline))} {$event->location}, {$event->boro} {$event->body} ";
		$share_data = array('url'=>$url, 'title'=>$event->title, 'description'=>$description);
	?>
	<div class="share-this-text">Share this Event!</div>
	<div class="share-toolbox" id="toolbox_<?=$event->hash_id?>"></div>
    <a id="atcounter_<?=$event->hash_id?>" class="addthis_bubble_style"></a>
    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=homeworknyc"></script>
	<script type="text/javascript">
	
		var tbx = document.getElementById("toolbox_<?=$event->hash_id?>"),
		svcs = {facebook: 'Facebook', google: "Google", email: 'Email', print: 'Print',  expanded: 'More'};
        
		for (var s in svcs) {
			tbx.innerHTML += '<a class="addthis_button_'+s+'"></a>';
		}
		
		addthis.toolbox("#toolbox_<?=$event->hash_id?>", {username:'homeworknyc'}, <?=JSON_encode($share_data)?>);
		addthis.counter("#atcounter_<?=$event->hash_id?>");
		
	</script>
	
	<!-- AddThis Button END -->
	</div>

</div>