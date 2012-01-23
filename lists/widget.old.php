<?
	include("_lists_core.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>ListIt - powered by HomeWorkNYC</title>
	<?=js()?>
	<?=css()?>
	<script type="text/javascript" charset="utf-8">
		wskey = "VPnMvg8kQ0CkElqTdoYKiMpOChql73nORdo1w5fXb5KNveKLwk1SqgiDpfWAX28vi2OAb1xa5yATOOCY";
		nypl_user_id = '<?=$nypl_user_id?>';
		$(document).ready(function() {
			// $('#searchWorldCat').submit(function() {
			// 	q = $('#searchterm').val();
			// 	search_worldcat(q);
			// 	return false;
			// });
			$('#searchWorldCat').submit(function() {
				show_status();
				q = $('#searchterm').val();
				$('#lists').load("_ajax_results.php?noLogin=true&wc_str="+q.replace(/\s/g, '_'));// made regex o handle more than 1 space.
				return false;
			});
			$('.page_nav').live('click', function() {
				if ( $(this).attr('id') == 'next' ) {
					page = parseInt($('#paginate #current_page').html()) + 1;
				} else {
					page = parseInt($('#paginate #current_page').html()) - 1;
				}
				q = $('#searchterm').val();
				$('#lists').load("_ajax_results.php?noLogin=true&page="+page+"&wc_str="+q.replace(' ', '_'));
			})
			
			$('#myLists').load('_ajax_results.php');
			
			$('#myLists div.listbuttons').live('click',function() {
				if($(this).html() != '') {
					$('#myLists div.listbuttons').slideUp();
					$('#lists').hide();
					$('#lists').html('<span id="loading">LOADING...</span>').slideDown('fast');
					$('#lists').load('_ajax_results.php?noLogin=true&list=<?=$nypl_user_id?>-'+$(this).html(), function() {$('#lists').hide().slideDown()});
				}
			})
			$('#tag_cloud span').live('click', function() {
//				$('#add_tag').val($('#add_tag').val() + '; '+ $(this).html());
				$('#add_tag').val($(this).html());
//				$('#add_button').click();
//				$('#tag_editor').slideUp();
			})
			$('#cancel_button').live('click', function() {
				$('#tag_editor').slideUp();
			})
			$('.remove_from_list').live('click', function() {
				alert($(this).attr('id'));
				$('dropstuff').load('_ajax_results.php?remove='+$(this).attr('id'));
			});
			$('#listsToggle').live('click', function() {
				$('#myLists div.listbuttons').slideToggle();
			})
			
			$('.manage_lists').live('click',function() {
				// if (event.button != 0) {// wasn't the left button - ignore
				// 	return true;
				// }
				item_dom = $(this).closest('.feed_item');
				item_id = item_dom.attr('id');
				serialized_item_data = $('#'+ item_id + ' div.serialized_item_data').html();

				$('#'+ item_id + ' div.tag_section').append($('#tag_editor'));
				$('#current_item').val(item_id);
				$('#tag_editor').slideDown();
				$('#add_tag').val('').focus();
//				$('#add_'+$(this).attr('id')).show().focus();
			});
			$('#add_button').live('click', function() {
				$('#dropstuff').load("_ajax_results.php?add_tag=true", {
					nypl_user_id:nypl_user_id, 
					noLogin:true,
					item_id:$('#current_item').val(), 
					tags:$('#add_tag').val(),
					serialized_item_data:serialized_item_data
				},function(data) {

				$('#tag_editor').hide();// hide tag editor
				// stick new tag in place for use as selector ONLY if it isn't there already.
//				$('#tag_cloud').append('<span>'+$('#add_tag').val()+'</span>');

				// add new tag to ITEM display
//				$('#'+ item_id + ' div.tags').append('<a href="?list=<?=$nypl_user_id?>-'+$('#add_tag').val()+'">'+$('#add_tag').val()+'</a>');// this one is clickable but broken
				$('#'+ item_id + ' div.tags').append( '; ' + $('#add_tag').val());

				//add collected item to $user_items[] *** may not be necesary since there are more page reloads now.

				});

			});
			
			
		});
		function show_status() {
			
		}
	</script>
	<link rel="stylesheet" href="styles.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=homeworknyc"></script>
</head>
<body>
	<div id="scroller" class="<?=$platform?>">
		<form id="searchWorldCat" action="" method="post">
			<div align="center">
				<strong id="logotype">ListIt <span class="beta">[beta]</span></strong><br />
				<input type="text" name="searchterm" value="" id="searchterm" />
					<input type="submit" id="search_button"  value="Go &rarr;" />
			</div>
			<div id="autocomplete">

			</div>
			<!-- <div id="search_result">

			</div> -->
			 <div class="roundCorners" id="searchcontrol"></div>

			<?/* <select id="myLists" name="myLists">
				<option value="">- View your lists -</option>
				<?php foreach($user_tags as $user_tag):?>
				<option value="<?=$user_tag?>"><?=$user_tag?></option>
				<?php endforeach;?>
			</select> */?>
			<div id="myLists">
			</div>

			<br clear='right'>
			<div class="instructions">
			<?php if (isset($_REQUEST['wc_str'])): ?>
				Click on "More Info" to find out more about a specific title. If you want to add something to one of your lists, click on "add to my lists." In the box that opens, type in the name of the list.  For example, if you want to create or add to  list that you already created of Best Manga Ever you would type Best Manga Ever in the textbox. You can see all of the lists you've created - and share them with friends - by clicking on View my Lists and selecting the list you want to see and/or share.
			<?php else:?>
				Type in the subject, theme, title, or author, of an item you would like to put on your list.  Then click Go.
			<?php endif;?>
			</div>
				
			<div id="myLists">
			
			</div>
		</form>

		<div id="lists">

		</div>
		<div id="dropstuff">
			<!-- this is a div in which to target ajax calls that don't need any display but may return chunk of javascript-->
		</div>
		
		<?php include('../_widget.footer.php');?>

	</div>
</body>
</html>

