<li>

		<a href="heheh"><?=$event->title;?></a> 
		<a href="jsjd"><?=$event->body;?></a>
		<a href="ahshs"><?=date( 'D, M jS, Y', strtotime($event->dateline));?> <?=$event->location;?>, <?=$event->boro;?></a>
	<!-- <div class="date_location">
		<div class="dateline">
			<?=date( 'D, M jS, Y', strtotime($event->dateline));?>
		</div>
		<div class="location">
			
		</div>
	</div>
	<div class="event_title_description">
		<div class="event_title"><?=$event->title;?></div>
		<?=$event->body;?>
	</div> -->
</li>

