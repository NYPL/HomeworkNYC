<div id="tag_editor">
	<div id="tag_cloud">
		<span><?=implode('</span>; <span>', $userInfo['user_tags'])?></span>
	</div>
	<input 
		class="add_tag"
		id="add_tag" 
		name="add_tag"
		value="">
	<input type="hidden" name="current_item" value="" id="current_item">
	<input type="button" name="Add" value="Add" id="add_button">
	<input type="button" name="Cancel" value="Cancel" id="cancel_button">
</div>

