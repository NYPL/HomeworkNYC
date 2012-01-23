<?
	include("_lists_core.php");
	if(isset($_REQUEST['list'])) {
		$list = array_pop(explode('-',$_REQUEST['list']));
		
	
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>HomeWorkNYC: Lists</title>
	<meta name="title" content="<?=$list;?>" /> 
	<meta name="description" content="<?=$share_description?>" /> 
	<link rel="image_src" href="/widgets/media/hwNYC_thumb.gif" / >
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<?=js()?>
	<?=css()?>
	<link rel="stylesheet" href="styles.css" type="text/css" media="screen" title="no title" charset="utf-8">
	
</head>
<body>
	<div id="scroller" class="<?=$platform?>">
		<form id="searchWorldCat" action="" method="post">
			<div align="center">
				<strong id="logotype">ListIt</strong><br />
			</div>
			<div id="autocomplete">

			</div>
			<!-- <div id="search_result">

			</div> -->
			 <div class="roundCorners" id="searchcontrol"></div>
			<div id="myLists">
			
			</div>
		</form>

		<div id="lists">
			<!-- my lists go here. -->
			<?php	
			$shared_list = true;
			if(isset($_REQUEST['list'])) {
				$list = array_pop(explode('-',$_REQUEST['list']));
				print "<h3 style='margin:4px'>{$list}</h3>";
				include ('../_share_url.php');
				foreach($userInfo['user_items'] as $user_item) {
					$tags = explode(';', str_replace('; ', ';',$user_item->tags));
					
					if(in_array($list, $tags)) {
							$item = unserialize(stripcslashes($user_item->serialized_item_data));
							include('_wc_item.tpl');
					}
				}
			}
			
			?>
		</div>
		
		<?php include('../_widget.footer.php');?>

	</div>
</body>
</html>

