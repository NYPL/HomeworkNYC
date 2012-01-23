<?php
	$striate = isset($striate)?$striate:0;
	$striate++;
?>
<div class='feed_item color_<?=($striate % 2)?>' id="<?=escape_for_css($item['id'])?>">
	<?php
		if(isset($userInfo['user_item_tags'][escape_for_css($item['id'])])) {
			$title_prefix = '*';
			$item['tags'] = $userInfo['user_item_tags'][escape_for_css($item['id'])];
		} else {
			$title_prefix = '';
		}
		$item['author'] = trim($item['author']);
	?>
	<span class='category'><a target='book' href='<?=$item['link_']?>'>[more info]</a></span> 
	<!-- <div class='id'><?=$item['id']?></div> -->
	<?=show_book_cover($item)?>
	<div class='title'><?=$title_prefix.$item['title']?></div>
	<div class='description'><?=$item['summary']?></div>
	<?php if(!$shared_list):?>
		<div class="tools">
			<div class="tag_section">
				<?php if(isset($_REQUEST['list'])):?>
				<span style="cursor:pointer;" class="remove_from_list custom" id="<?=$_REQUEST['list'].'-'.escape_for_css($item['id'])?>">remove from <em><?=$list_name?></em></span><br>
				<?php endif;?>
				<span style="cursor:pointer;" class="manage_lists" id="tag_<?=escape_for_css($item['id'])?>">add to my lists</span>
			</div>
		</div>
		<div class='tags'><?php
			print str_replace(';','; ', $item['tags']);
			// foreach(explode(';', $item['tags']) as $tag) {
			// 	$tag = trim($tag);
			// 	print "<a href='?list={$nypl_user_id}-{$tag}'>{$tag}</a> ";
			// }
			;?>
		</div>
		<div class="serialized_item_data"><?=serialize($item)?></div>
	<?php endif;?>
</div>