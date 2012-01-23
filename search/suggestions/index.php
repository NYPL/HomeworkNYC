<?php 
include("../../_db.php"); 
include("../../_core.php"); 
include("../../_platform.php"); 


// this is NEARLY identical to the autocomplete in SEARCH but it may fork
// might make a getJSON or doAutocomplete() on _db.php!!!
$action = isset($_REQUEST['action'])?$_REQUEST['action']:false;

if($action=='autocompleteterms') {

	$stub = $_GET["q"];//$_REQUEST['searchstub'];
	$terms = '';
	$results = doSelect('crosswalk_terms', 'term', "WHERE concat(' ', term, ' ', thesaurus, ' ') LIKE '%{$stub}%' ORDER BY term" );
	while ($row = mysql_fetch_assoc($results)):
		$terms .= $row['term']."\n";
	endwhile;
	die($terms);

} elseif($action=='show_thesaurus') {

	$stub = $_GET["q"];//$_REQUEST['searchstub'];
	$terms = '';
	$results = doSelect('crosswalk_terms', 'thesaurus', "WHERE term = '{$stub}'" );
	while ($row = mysql_fetch_assoc($results)):
		$terms .= $row['thesaurus'];//div('term searchable', 'edit-term_'.escape_for_css($row['id']), $row['term']);
	endwhile;
	die($terms);

} elseif($action=='show_all_terms') {
	$terms = '';
	$results = doSelect('crosswalk_terms', 'term', "WHERE 1 = 1 ORDER BY term" );
	while ($row = mysql_fetch_assoc($results)):
		$terms .= "<div class='term_from_show_all'>{$row['term']}</div>\n";
	endwhile;
	die($terms);

} elseif($action=='update_thesaurus') {

	$data = array('thesaurus' => $_POST['edit_thesaurus'], 'ip'=>client_ip(), 'userid'=>$_POST['userid'], 'updated'=>date('Y-m-d H:i:s'));
	$results = doUpdate('crosswalk_terms', $data, "term = '{$_POST['searchterm']}'");

} elseif($action=='newterm') {

	$stub = $_GET["q"];//$_REQUEST['searchstub'];
	$terms = '';
	$term_data = array(
		'term'=>$stub,
		'thesaurus'=>'',
		'ip'=>$_SERVER['REMOTE_ADDR'],
		'category'=>'Community Submitted'
	);
	$results = doInsert('crosswalk_terms', $term_data );
	die('SAVED !');

} elseif($action=='delete') {
	
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title>HomeWorkNYC - Search - Suggestion Editor</title>
	<?=js();?>
	<?=css();?>
	<script type="text/javascript" charset="utf-8">
		var cookie_options = { path: '/', expires: 100000 };
		$(document).ready(function() {
			if($.cookie('homeworkNYC_search_suggestor_id')) {
				$('#userid').val($.cookie('homeworkNYC_search_suggestor_id'))
			} else {
				email = prompt('Please provide your email address to continue');
				if(valid_email(email)) {
					$.cookie('homeworkNYC_search_suggestor_id', email, cookie_options);
				}
			}
			$('#javascript_test').hide();
			$("#searchterm").autocomplete("<?=$_SERVER['SCRIPT_URI']?>?action=autocompleteterms", {
				minChars:2, 
				matchSubset:0, 
				matchContains:1, 
				selectOnly:0, 
				onItemSelect:onAutoCompleteSelection
			});
			$("#searchterm").focus(function() {
				$('#show_thesaurus').hide();
				$('edit_thesaurus').val('');
				$('#new_term').html('');
			});
			$('.confirm_new_term').click(function() {
				if($(this).attr('id') == 'cancel') {
					$('#new_term').html('').hide();
				} else {
					$('#new_term').load("<?=$_SERVER['SCRIPT_URI']?>?action=newterm&q=<?=$_POST['searchterm'];?>", function() {
						onAutoCompleteSelection();
					});
				}
			});
			$('#update_thesaurus').click(function() {
				$('input#action').val('update_thesaurus');
				$('#suggestionForm').submit();
			});
			$('#show_all_button').click(function() {
				$('#show_all_terms').load("<?=$_SERVER['SCRIPT_URI']?>?action=show_all_terms")
			});
		});
		
		function onAutoCompleteSelection() {
			$('#show_thesaurus textarea#edit_thesaurus').load("<?=$_SERVER['SCRIPT_URI']?>?action=show_thesaurus&q="+escape($('#searchterm').val()), {}, function() {
					$('#display_term').html($('#searchterm').val());
					$('#show_thesaurus').slideDown();
			});
		}
	</script>
	
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="no title" charset="utf-8">
	
</head>

<body>
<?php 
// if(isset($_POST['emailaddress_for_cookie'])):
// 
// 
// 	setcookie('homeworkNYC_search_style')
// 
// elseif(!isset($_cookie['homeworkNYC_search_style'])):
?>
		<!-- <form style="text-align:center" action="" method="post" accept-charset="utf-8">
			<h3>please provide an email address to continue</h3>
			<input type="text" name="emailaddress_for_cookie" value="" id="emailaddress_for_cookie">
			<p><input type="submit" value="Continue &rarr;"></p>
		</form> -->
<?php 
	// 	die();
	// endif;
?>
	<div id="scroller">
		<h3 id="javascript_test" style="text-align:center;padding:6px;color:white;background-color:red">Javascript is required to participate</h3>
		<form id="suggestionForm" action="" method="post">
			<input type="hidden" name="action" value="" id="action">
			<input type="hidden" name="userid" value="" id="userid">
			<div align="center">
				<strong id="logotype">HomeWorkNYC: Suggestion Editor</strong><br />
				<input type="text" name="searchterm" value="" id="searchterm" />
				<div id="instructions">
					Search for the term you wish to modify. This search box uses the <em>SUGGESTION ENGINE</em> so you can search for either SUGGESTED TERMS or THESAURUS TERMS. If the term you are looking for is not in the <em>SUGGESTION ENGINE</em> you may add it.
				</div>
			</div>
				<?php if(isset($_POST['searchterm']) && $action == ''): ?>
					<div id="new_term">
						Please confirm the you would like to add <strong><?=$_POST['searchterm'];?></strong> to the <em>Suggestion Engine</em>.
						<div class="buttons">
							<input class="confirm_new_term" type="button" name="add_it" value="add it" id="add_it">
							<input class="confirm_new_term" type="button" name="cancel" value="cancel" id="cancel">
						</div>
					</div>
					<script type="text/javascript" charset="utf-8">
						$('#searchterm').val("<?=$_POST['searchterm'];?>");
					</script>
				<?php endif;?>
			<div id="edit_term">
				
			</div>
			<div id="show_thesaurus">
				Thesaurus Terms for <span id="display_term"></span>:<br />
				<textarea name="edit_thesaurus" id="edit_thesaurus" rows="8" cols="40"></textarea>
				<div id="thesaurus_instructions">(use commas to delimit the terms)</div>
				<input type="button" name="update_thesaurus" value="Save Changes" id="update_thesaurus">
				<hr>
				<input type="button" name="delete_term" value="delete this term" id="delete_term">
				
			</div>
		</form>
		

		<div id="user">
			Your IP: <?=client_ip();?><br>
			Time: <?=date(DATE_RFC822 );?>
		</div>

	<?php include('../../_feedback_form.php');?>
	</div>
	
	
</body>
</html>
