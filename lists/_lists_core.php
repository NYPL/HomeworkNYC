<?
	include("../_platform.php");
	include("../_db.php"); 
	include("../_core.php"); 

	$share_description = "A list I have gathered.";

	if($platform == 'facebook') {
		include_once '../lib/facebook.php';
		$facebook = new Facebook(array(
			    'appId'  => 'dded64754816e449518b62e0f7ae2869',
			    'secret' => '01b1c74e6c8566163d7f2554626b73c9',
			    'cookie' => true,
			    'domain' => 'homeworknycbeta.org'
			));
			$session = $facebook->getSession();
			if (!$session) {
			     $url = $facebook->getLoginUrl(array(
			               'canvas' => 1,
			               'fbconnect' => 0
			           ));
				echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			} else {
			    try {
			        $nypl_user_id = 'fb_'.$facebook->getUser();
			        $me = $facebook->api('/me');
			//        $updated = date("l, F j, Y", strtotime($me['updated_time']));
			//       echo "Hello " . $me['name'] . "<br />";
			//        echo "You last updated your profile on " . $updated;
			    } catch (FacebookApiException $e) {
			        echo "Error:" . print_r($e, true);
			    }
			}		
			

	





	




	} elseif($platform == 'Google') {
		 /* GOOGLE userID parser section
		$_REQUEST['iGoogle_id'] is passed by the XML that is parsed and displayed by google.. it grabs the ID using Google's javascript API and then loads an iFrame. Seemed dubious but they use it themselves in some of there developer gadgets.
		HOWEVER since this URL variable is only available to PHP the first time... it is parsed int oa PHP cookie.
		HOWEVER (2) the iframe shoudl still have it in its URL params... which means JS can get at it at any point! this could prove usefull...

		I should look int this same issue on FB as a possible double-check of the uid info.
		*/
		if(isset($_REQUEST['iGoogle_id'])) { 
			setCookie('googleID', $_REQUEST['iGoogle_id'], 0);
			$_COOKIE['googleID'] = $_REQUEST['iGoogle_id'];
			$nypl_user_id = 'g_'.$_REQUEST['iGoogle_id'];
		} elseif(isset($_COOKIE['googleID'])) {
			$nypl_user_id = 'g_'.$_COOKIE['googleID'];
		} else {
			$nypl_user_id = $_REQUEST['nypl_user_id'];//'cookie not set!';
		}
	} else {
		setCookie('googleID', '', time() - 3600);
	}

//moved into function	$get_user_items_WHERE = "WHERE user_id = '{$nypl_user_id}'";


	if(isset($_REQUEST['list'])) { // lists are shareable and thus may be viewed by someone with a different ID
		// Im sure I noted this elsewhere but IDs must be obfuscated!!! (eventually)
		$listInfo = explode('-',$_REQUEST['list']);
		if($listInfo[0] != $nypl_user_id) {
			$shared_list = true;
			$get_user_items_WHERE = "WHERE user_id = '{$listInfo[0]}' AND tags LIKE '%{$listInfo[1]}%'";
		}
		$list_name = $listInfo[1];
	} 
	// elseif (isset($_REQUEST['remove'])) {
	// 	$listInfo = explode('-',$_REQUEST['remove']);
	// 	doQuery("UPDATE list_items SET tags = replace(tags, '{$listInfo[1]}', '')  WHERE user_id = '{$listInfo[0]}' AND item_id = '{$listInfo[2]}'");
	// }

$userInfo = get_user_info();

function get_user_info() {
	global $nypl_user_id;
	$get_user_items_WHERE = "WHERE user_id = '{$nypl_user_id}'";
	$get_user_items = doSelect('list_items', '*', $get_user_items_WHERE);
	// create php array of $user_items
	$userInfo['user_items'] = array();
	$userInfo['user_tags'] = array();
	$userInfo['user_item_tags'] = array();
	while($row = mysql_fetch_object($get_user_items)) { // loop over all items
		if(trim(str_replace(';', '',$row->tags)) == '') { // get rid of empty records (no tags)
			doDelete('list_items', "id = {$row->id}");
		} else {
			$userInfo['user_item_tags'][$row->item_id] = $row->tags;
			$userInfo['user_items'][] = $row;
			foreach(explode(';', $row->tags) as $tag) {
				$tag = trim($tag);
				if(!in_array($tag, $userInfo['user_tags']) && $tag != '') {
					$userInfo['user_tags'][] = $tag;
				}
			}
		}
	}
	return $userInfo;
}


function show_book_cover($item) {
	$isbn_parts = explode('urn:ISBN:',$item['dc']['identifier']);
	$book_cover_src = 'http://contentcafe2.btol.com/ContentCafe/Jacket.aspx?UserID=NYPL49807&Password=CC68707&Return=1&Type=S&erroroverride=1&Value=' . $isbn_parts[1];
	return '<img class="bookcovers" src="'.$book_cover_src.'" height="" width="" alt="Thumb" />';
}
