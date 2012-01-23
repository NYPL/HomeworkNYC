<?php	
include("_lists_core.php");


// function paginate($total, $curr_page) {
// 	print '<div id="paginate">';
// 	print " Page <span id='current_page'>{$curr_page}</span> of {$total} total results ";
// 	if($curr_page > 1) {
// 		print " <span class='page_nav' id='prev'>prev</span>";
// 	}
// 	print " <span class='page_nav' id='next'>next</span>";
// 	print '</div>';
// }

	
if(isset($_REQUEST['list'])) {
	$list = array_pop(explode('-',$_REQUEST['list']));
	print "<h3 style='margin:4px'>{$list}</h3>";
//	include ('../_share_url.php');
	$share_data = array('url'=>"{$CFG_site_url}widgets/lists/shared_list.php?list={$_REQUEST['list']}", 'title'=>$list, 'description'=>$share_description);
	?>
	<div class="addthis_toolbox addthis_default_style" style="text-align:right">
		<a class="addthis_button" id="toolbar_<?=$event->hash_id?>" href=""></a>
		<script type="text/javascript">
		addthis.button('#toolbar_<?=$event->hash_id?>', {username:'homeworknyc'}, <?=JSON_encode($share_data)?>);
		</script>
	</div>
	<?php
	include('_tag_editor.php');
	
	foreach($userInfo['user_items'] as $user_item) {
		$tags = explode(';', str_replace('; ', ';',$user_item->tags));
		
		if(in_array($list, $tags)) {
			$item = unserialize(stripcslashes($user_item->serialized_item_data));
			include('_wc_item.tpl');
		}
	}
	
} elseif (isset($_REQUEST['wc_str'])) {
	$page = isset($_REQUEST['page'])?$_REQUEST['page']:1;
	$items_per_page = 20;
	// WORLDCAT API key
	$wskey = 'VPnMvg8kQ0CkElqTdoYKiMpOChql73nORdo1w5fXb5KNveKLwk1SqgiDpfWAX28vi2OAb1xa5yATOOCY';
	require_once('../lib/magpierss/rss_fetch.inc');
	$url='http://worldcat.org/webservices/catalog/search/opensearch?q='.$_REQUEST['wc_str'].'&wskey='.$wskey.'&count='.$items_per_page.'&start='.(($page-1)*$items_per_page);

	$rss = fetch_rss( $url );
	include('_tag_editor.php');
	paginate($rss->channel['opensearch']['totalresults'], $page, $items_per_page);
	foreach ($rss->items as $item) {
		include('_wc_item.tpl');
	}
	paginate($rss->channel['opensearch']['totalresults'], $page, $items_per_page);
	

} elseif(isset($_REQUEST['add_tag'])) {
	// create an initial array of SUBMITTED tag info.
	$tag_data = array(
		'item_id'=>$_REQUEST['item_id'], 
		'serialized_item_data'=>$_REQUEST['serialized_item_data'],
		'tags'=>$_REQUEST['tags'], 
		'user_id'=>$nypl_user_id);

// strip dupes from tags array
//$tag_data['tags'] = explode()

	//check if this user / item combo exists in the database already
	// IF so UPDATE that record by adding this tag.
	// ELSE insert a new record with this tag
	
	if(isset($userInfo['$user_item_tags'][$_REQUEST['item_id']])) {
		if (!in_array($_REQUEST['tags'], explode(';',$user_item_tags[$_REQUEST['item_id']])	)) {
			$tag_data['tags'] = $userInfo['user_item_tags'][$_REQUEST['item_id']].';'.$tag_data['tags']; 
			$result = doUpdate('list_items', $tag_data, "item_id = '{$_REQUEST['item_id']}' AND user_id = '{$nypl_user_id}'");
		}
	} else {
//		$tag_data['tags'] .= trim(str_replace(';', '', $_REQUEST['tags'])); 
		$result = doInsert('list_items', $tag_data);
	}
} elseif (isset($_REQUEST['remove'])) {
	$listInfo = explode('-',$_REQUEST['remove']);
	doQuery("UPDATE list_items SET tags = replace(tags, '{$listInfo[1]}', '')  WHERE user_id = '{$listInfo[0]}' AND item_id = '{$listInfo[2]}'");
}

$userInfo = get_user_info();

$tag_nav_options = "<div id=\"listsToggle\">View your lists</div>";

foreach($userInfo['user_tags'] as $user_tag) {
	$tag_nav_options .= "<div class=\"listbuttons\" id=\"{$user_tag}\">{$user_tag}</div>";
}
?>
<script type="text/javascript" charset="utf-8">
	$('#myLists').html('<?=$tag_nav_options?>');
</script>



