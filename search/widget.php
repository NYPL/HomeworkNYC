<?php

require("OAuth.php");  
include("../_db.php"); 
include("../_core.php");
include("../_platform.php");
include("../Mobile_Detect.php");
function get_external_data($url) {
	// Make the request
	$xmlstr = file_get_contents($url);

	// Retrieve HTTP status code
	list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);//die('Your call to Yahoo Web Services failed and returned an HTTP status of 503. That means: Service un'.$status_code);
	// Check the HTTP Status code
	switch($status_code) {
	
		case 200:
			// Success
			break;
		case 503:
			die('Your call to Yahoo Web Services failed and returned an HTTP status of 503. That means: Service unavailable. An internal problem prevented us from returning data to you.');
			break;
		case 403:
			die('Your call to Yahoo Web Services failed and returned an HTTP status of 403. That means: Forbidden. You do not have permission to access this resource, or are over your rate limit.');
			break;
		case 400:
			// You may want to fall through here and read the specific XML error
			die('Your call to Yahoo Web Services failed and returned an HTTP status of 400. That means: Bad request. The parameters passed to the service did not match as expected. The exact error is returned in the XML response.');
			break;
		default:
			die('Your call to Yahoo Web Services returned an unexpected HTTP status of:' . $status_code);
	}
	return $xmlstr;
}

if(isset($_REQUEST['s']) || isset($_REQUEST['loadmore'])) { // this is the pagination OR the initial search
	$results_markup = '';
	$s = str_replace(' ', '+',trim($_REQUEST['s']));
	$t = (isset($_REQUEST['t']))?$_REQUEST['t']:'web';

	//this is gale search, need have gale account, username and password. and contact gale to get ip authentication
	if($t == 'gale') {
		set_time_limit(120); 
		
		$gale_url ='http://sru.galegroup.com/ITOF?startRecord=1&maximumRecords=25&operation=searchRetrieve&version='
				  .'1.1&x-username=yourusername&query=dc.title='.$s;
	
		$xml_str = file_get_contents($gale_url,0);
		$xml_str = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml_str); 
		$xml_str = iconv("GB18030", "utf-8", $xml_str);
		$xml = new SimplexmlElement($xml_str, TRUE);
	
		$count = 0;
		$gale_results = array();
		$lists=$xml->zsrecords;
		if($lists)
		{
		  foreach($lists->zsrecord as $item) {
		  $gale_results[$count]->title = $item->zsrecordData->dcdc->dctitle[0];
		  $gale_results[$count]->abstract 	= "";// $item->zsrecordData->dcdc->dcdescription[0];
		  $gale_results[$count]->sourceurl	= 'geo.php?link='.urlencode($item->zsrecordData->dcdc->dcidentifier[0]);
		  $gale_results[$count]->url		= 'geo.php?link='.urlencode($item->zsrecordData->dcdc->dcidentifier[0]);
		  $count++;
		}
	}
	$total_hits=$count;
	
	}
	//yahooboss search, you need create account on yahooboss to get key and secret
	else 
	{   //put your key and secret here
		$cc_key  = "yourkey";  
		$cc_secret = "yoursecret";  
		$url = "http://yboss.yahooapis.com/ysearch/{$t}";  
		$args = array();  
		$args["q"] = $s;  
		$args["format"] = "json";  
		   
		$consumer = new OAuthConsumer($cc_key, $cc_secret);  
		$request = OAuthRequest::from_consumer_and_token($consumer, NULL,"GET", $url, $args);  
		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);  
		$url = sprintf("%s?%s", $url, OAuthUtil::build_http_query($args));  
		$ch = curl_init();  
		$headers = array($request->to_header());  
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
		curl_setopt($ch, CURLOPT_URL, $url);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
		$rsp = curl_exec($ch);  

		$xmlObj = json_decode($rsp);
	}
    /*
	$results_markup .= "<div>total hits:{$total_hits}</div>";

	print "<pre>";
	var_dump($xmlObj->ysearchresponse);
	print "</pre>";
	*/
	
	if($t == 'gale') {			$abstracted_results = $gale_results;
	} elseif($t == 'images') {	$abstracted_results = $xmlObj->bossresponse->images->results; $total_hits = $xmlObj->bossresponse->images->count;
	} elseif ($t == 'news') {	$abstracted_results = $xmlObj->bossresponse->news->results; $total_hits = $xmlObj->bossresponse->news->count; //print_r('ddddddd');  print_r($xmlObj); 
	} else {					$abstracted_results = $xmlObj->bossresponse->web->results; $total_hits = $xmlObj->bossresponse->web->count;
	}
  
	$useful_fields = array(
		'abstract',
		'refererurl',
		'source',
		'sourceurl',
		'date',
		'time'
	);

    $item_count=0;
	if($t == 'gale') {
		$results_markup .= "<div id='gale-ref'>Reference Articles Powered by <img src='../media/gale.png' width=48 height=18 /></div>\n";
	}	
	if($abstracted_results)
	{
		foreach($abstracted_results as $r) {
			$item_count++;
			if($item_count>25)
				$results_markup .= '<div class="searchResult clearfix"  style="display:none">';
			else
				$results_markup .= '<div class="searchResult clearfix">';
			if($t == 'images' && !empty($r->thumbnailurl)) {
				$results_markup .= "<img height=\"70\" align=\"left\" src=\"{$r->thumbnailurl}\" />";
			}
			
			if ($platform == "app")
				$results_markup .= "<a class='result_title_link' cbLink='{$r->url}'>{$r->title}</a>";
			else
				$results_markup .= "<a target='_blank' class='result_title_link' href='{$r->url}'>{$r->title}</a>";
			
			$results_markup .= "<div>{$r->abstract}</div>";
			$results_markup .= '</div>'."\n";
		}
	}
	if($total_hits>25) {
		$results_markup .= "<div id='loadmore'>More Results</div>\n";
	}
	die($results_markup);
	
} 
elseif(isset($_REQUEST['suggestions'])) {
	/*	this is a shortuct to avoid creating multipl pages. If AJAX request is called, it loads the same page, different URL params and then die() before the page renders.
		This block queries the database for matching terms as the user types.
		returns terms seperated by new lines
	*/

	$stub = $_GET["q"];// assumes this exists
	$terms = '';// initialize this string to hold matching strings
	$results = doQuery("SELECT term FROM crosswalk_terms WHERE concat(' ', term, ' ', thesaurus, ' ') LIKE '%{$stub}%' ORDER BY term");
	while ($row = mysql_fetch_assoc($results)):
		$terms .= $row['term']."\n";
	endwhile;
	if(!empty($terms)){
		die($stub."\n".$terms);		
	} else {
		die();
	}
} 


?>

<html>

<head>
	<title>SearchIt - powered by HomeWorkNYC</title>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />

	<?php 
	$detect = new Mobile_Detect();
	$mobilePlatform = '';

	if ($detect->isMobile()) {
		$mobilePlatform = 'mobile';
		mobileResources();
	}
	?>
	<?=js()?>
	 <!-- BEGIN yolink -->
    <script src="http://cloud.yolink.com/yolinklite/js/tigr.jquery-1.4.2-min.js" type="text/javascript"></script>
    <script type="text/javascript" src="http://cloud.yolink.com/yolinklite/js/v2/yolink-2.0.js"> </script>
	<!---END yolink-->
    <script type="text/javascript" src="../lib/modernizr-transition.js"> </script>
    <script src="http://jsconsole.com/remote.js?AAB51738-3563-4CEB-9D83-F7AFC1AADCB9"></script>
	<?=css()?>
	
	<script type="text/javascript" charset="utf-8">
		
		var cookie_options = { path: '/', expires: 30 };
		var supportTransition = false;
		var stopPopup = false;
		
		function visitedTwice(){
			if(!($.cookie('search-visited'))) {
				$.cookie('search-visited', '1', cookie_options);
				//console.log("not yet visited");
				stopPopup = false;
				return false;
			}
			
			else if ($.cookie('search-visited') == 1) {
				//console.log($.cookie('search-visited'));
				$.cookie('search-visited', '2', cookie_options);
				//console.log("visited once");
				stopPopup = false;
				return false;
			}
			
			else {
				//console.log($.cookie('search-visited'));
				stopPopup = true;
				return true;
			}
			
		}
		
		$(document).ready(function() {
			<?php
				//determine if page request is coming from an app instead of desktop/mobile browser
				if ($platform == 'app') {
			?>
				$(".result_title_link").live("click", function(e){
					var cbURL = $(this).attr('cbLink');
					parent.postMessage(cbURL,'*'); 
				});	
			<?php
				}
			?>	
			
			/* check for css3 transition support */
			if (Modernizr.csstransitions) {
				supportTransition = true;
				$("#search-popuphelp").css("opacity",0).addClass("fadeTrans");
			}
			init_yoLink();
			
			$('#searchForm').submit(function(event) {
				if(visitedTwice()) $("#search-popuphelp").css("display","none");
				$(".db-content").css("display","none");
				$('#loading').fadeIn();
				$('#mainContent').fadeOut();
				$('#introText').fadeOut();
				
				event.preventDefault();
				
				t = $('#ddl_search').val();
				s = encodeURI($('#search_string').val());
				s = $('#search_string').val();
				
				$.post('?s='+s+'&t='+t,function(data) {
					var keyword = s;
					
					if(keyword && keyword.length > 0) {
						tigr.yolink.Widget.options.keywords = keyword;
					}
					
					$('#results').html(data);
					$('#loading').fadeOut();
					
                    $('#mainContent').fadeIn(function(){
						if(!stopPopup) {
							if(supportTransition) {
								$('#search-popuphelp').delay(500).css("display","block").css("opacity", 1);
								
							}
							else $('#search-popuphelp').delay(500).fadeIn();
						}
					});

                    $('#yolink_trigger').fadeIn();
					$('#yolink_trigger span').html('Activate');
				});
			})
			
            $('div#yolink_trigger a').live('click', function() {
				if($('#yolink_trigger span').html() == 'Activate') {
					$('#yolink_trigger span').html('Remove');
					$('#search-popuphelp:visible').fadeOut();
					visitedTwice();
					tigr.yolink.Widget.find();
				} else {
					tigr.yolink.Widget.removeAll();
					$('#yolink_trigger span').html('Activate');
					
				}
			})
			
			$('#ddl_search').change(function() {
				if($('#search_string').val() != '') {
					$('#searchForm').submit();
				}
			})
			
			$("#search_string").autocomplete("?suggestions=true", {
				minChars:2, 
				matchSubset:0, 
				matchContains:1, 
				selectOnly:0, 
				onItemSelect:onAutoCompleteSelection,
				formatItem:formatAutoComplete
			});

			$('#loadmore').live('click', function() {
					$('#loadmore').remove()
					$('.searchResult').css('display', 'block');
			})
			
			$('#close-btn').click(function (){
				if(supportTransition){
					$('#search-popuphelp').css("opacity", 0);
				}
				else $('#search-popuphelp').fadeOut();
				
				visitedTwice();
			});

		});

		
		function make_yoLinks() {
			return 'hi mom';
		}
		
		function onAutoCompleteSelection() {
			$('#searchForm').submit();
		}
		
		function formatAutoComplete(r,i,t) {
			str = r;
			if (i < 1) {
				str =  '<b>search for:</b> ' + str;
			} else if (i == 1) {
				str =  '<b>or try:</b> <br>' + str;
			}
			return str;
		}
		//need have yolink account get apikey
		function init_yoLink() {
			tigr.yolink.Widget.initialize(
			{
				display         : 'embed',
				getSearch       : 'div.searchResult',
				showTools       : 'result',
				keywords        : 's',
				selectAll       : true,
				share           : true,
				googledocs      : true,
				fblike          : 'local',
				tweet           : 'local',
				preview         : 'tab',
				//auto            : true,
				apikey          : 'yourapikey',//your api key
				showHide        : true
			} );
		}
	</script>
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<!--[if lte IE 9]>
		<style>
			#search_string { width: 55%; height: 30px; }
			#ddl_search { height: 35px; }
		</style>
	<![endif]-->	
</head>
<body>
	<div id="scroller" class="<?=$platform?> <?=$mobilePlatform?>">
	    <?php include('../_widget.header.php');?>
			<form id="searchForm" action="widget.php" method="post">
					<div id="search-controls-wrapper">

					<table id="search-controls">
						<tr>
						<td style="width: 100%;">
							<input type="text" name="search_string" value="" id="search_string"/>
						<td>
						<td style="width: 10px;">&nbsp;</td>
						<td style="width: 100px; vertical-align: middle;">
							<select id="ddl_search">
							  <option value="web">Web</option>
							  <option value="images">Images</option>
							  <option value="news">News</option>
							  <option value="gale">GALE</option>
							</select>
						</td>
						</tr>
					</table>

					</div>
					<div id="loading" class="round_corners">&nbsp;Loading...</div>
					<div id="mainContent">
					<div id="yolink_trigger">
						<a href="#"><span>Activate</span> <img src="../media/activate_yolink.png" width="33" align="absmiddle" height="12" alt="Activate Yolink"></a>
					</div>
					
					<div id="search-popuphelp">
							<div id="close-btn"></div>
							<div id="search-popupinner">
								<h3>Tap to Activate <img src="../media/yolink-small.png" style="vertical-align: middle" /></h3>
								<p>Do even more with what you find.
								<p><strong>Activate <img id="yolink-text" src="../media/yolink-small.png" height="15"> to:</strong></p>
								<ul>
									<li class="clearfix"><img class="social-btn" src="../media/share_yo.jpg" /><span>Share the Article<br />Cite the Source with <img src="../media/easybib-icon.gif" alt="EasyBib" />EasyBib</span></li>
									<li class="clearfix"><img class="social-btn" src="../media/share_docs.jpg" /><span>Create a Google Doc of the Article</span></li>
									<li class="clearfix"><img class="social-btn" src="../media/share_tweet.jpg" /><span>Tweet the Article on Twitter</span></li>
									<li class="clearfix"><img class="social-btn" src="../media/share_face.jpg" /><span>Share the Article on Facebook</span></li>
								</ul>
							</div>
						</div>					
						<div id="introText">
						<h1 id="search-it">SearchIt!</h1>
						<p>Homework help suggestions from teachers and librarians.</p>
						<p><strong>Type in what you want to search, then select where you want to search from the drop down menu:</strong></p>
						<ul class="search-types-list">
							<li>Websites</li>
							<li>Images (photos, drawings...)</li>
							<li>News sites</li>
							<li>On-line reference books, magazines &amp;<br class="mobile-lb" /> newspapers from <img style="vertical-align: middle;" src="../media/gale-logo-small.png" /></li>
						</ul>
						<p><strong>Still can't find what you need? Use your library card to log in to one of our many additional public library databases.</strong></p>
						<ul class="search-types-list" id="db-list">
							<li>Log in with your <a href="nypl-db.php">New York Public Library card</a></li>
							<li>Log in with your <a href="bpl-db.php">Brooklyn Public Library card</a></li>
							<li>Log in with your <a href="qpl-db.php">Queens Library card</a></li>
						</ul>
						</div>
						</form>
						<div id="results">
							<?php f ?>
						</div>
						
						<div id="powered-footer">
							<p>Powered by:</p>
							<p><img src="../media/nypl_logo.png" alt="NYPL" /><img src="../media/queenslibrary.png" alt="Queens Library" /><img src="../media/brooklynlibrary.png" alt="Brooklyn Library" /></p>
						</div>
					</div>
			
		<?php include('../_widget.footer.php');?>
	</div>
</body>
</html>

