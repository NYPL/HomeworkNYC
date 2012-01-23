<?php
	include("../_db.php");
	include("../_core.php");
	doLog('eventimport', '***** beginning _cache.php ******');
	include("_feeds.php");

	print '<!-- _cache.php -->';

	/* **************
	This file handles pulling latest feed data into local (mysql cache) for later display.
	handles duplicates and expired items
	stores serialized version of feed along with parsing the necesary attributes
	*/

	require_once('../lib/magpierss/rss_fetch.inc'); 					// http://magpierss.sourceforge.net/
	set_time_limit(120); 												// this can take a while.
	require_once('../lib/simplehtmldom/simple_html_dom.php'); 			// http://simplehtmldom.sourceforge.net/

	$mysqlDateFormat = 'Y-m-d H:i:s';

	pull_feeds_into_db();
	doLog('eventimport', 'start feed retrieval' . time());

	function pull_feeds_into_db() {
		global $feeds;
		$existing_feeds_result = doSelect('event_cache', 'hash_id');	// since I don't get a unique ID from each feed/event I create one by hashing the title & description
		$existing_feeds = array();
		while($row = mysql_fetch_object($existing_feeds_result)) {
			$existing_feeds[] = $row->hash_id;
		}
//print_r($existing_feeds);
		foreach($feeds as $feed) {// loop over the feeds
			$url = $feed['url'];
			error_reporting(E_ERROR);
			doLog('eventimport', 'start fetch: ' +$url+' @ '.time());
			$rss = fetch_rss( $url );				//this is a magpie function
			if ($rss) {								// is it a valid feed, has it data?
				foreach ($rss->items as $item) {	// loop over the items
//	print "*desc*************************************************************************\n";
//	print_r($item['description']);
//	print "*/desc*************************************************************************\n";
					$dateline = parse_date($item, $feed);// have to parse the date because I need it for the hash
					$hash_id = sha1($item['title'].$item['description'].$dateline); //create a unique-ish value for use as ID (to eliminate dupes)

//WHY AM I CHECKING UNIQUENESS OF AN EVENT?
//JUST REFRESH THE CACHE EVERY HOUR! or 6 hours
					if(!in_array($hash_id, $existing_feeds)) {				// check unique-ish hash against cache
						// assign values including some that need more 'extracting' than others. This would be much simpler if we had standardized RSS
						$location = parse_location($item, $feed);
						$summary  = parse_summary($item, $feed);
						$sql_item = array(
							'title'			=> $item['title'],
							'body'			=> $summary,
							'link'			=> $item['link'],
							'boro' 			=> $feed['boro'],
							'audience' 		=> $feed['category'],
							'dateline' 		=> $dateline,
							'location' 		=> $location,
							'hash_id'		=> $hash_id,
							'raw_feed_data'	=> serialize($item)
						);
//print "**SQLitem************************************************************************\n";
//print_r($sql_item);
						doInsert('event_cache', $sql_item);			// stick the items into the DB, including the raw RSS item object as serialized php data
						$existing_feeds[] = $hash_id;				// very clever... by adding the current hash, I strip dupes from the feeds
					}
				}
				doLog('eventimport', 'finish populating cache: '.$url.' @ '.time());

			} else {
				print "<div id=\"magpie_error\">";
				$mp_error = magpie_error();
				print $mp_error;
				doLog('eventimport', $mp_error);
				print "</div>";
			}
		}
		doDelete('event_cache', 'event_cache.dateline < date_sub(now(), INTERVAL 1 DAY )');
		doUpdate('config', array('value'=>time()) , "name = 'events_last_update'");	// update the value for when RSS was last checked
	}

	function parse_summary($html, $feed) {
		if($feed['boro'] != 'Queens'){
			$dom = str_get_html($html['description']);								// grab the description from RSS
			$element = $dom->find(".vevent .summary p", 0);
			return str_replace('<br>', '',$element->innertext);											// using simplehtmldom grab the element with class dtstart
		}
		return $item['description'];
	}
	function parse_date($html, $feed) {
		GLOBAL $mysqlDateFormat;
		if($feed['boro'] == 'Queens') {
			$dom = str_get_html($html['description']);								//grab desc
			$html = $dom->find("em",0);												// simplehtmldom gets first EM
			$return = date($mysqlDateFormat, strtotime(trim(array_pop(explode('&nbsp;&nbsp;&nbsp;', $html->innertext)))));	//just more HTML/string parsing, very suceptable to minor changes to the feed's HTML format
		} else {
			$dom = str_get_html($html['description']);								// grab the description from RSS
			$element = $dom->find(".dtstart", 0);											// using simplehtmldom grab the element with class dtstart
			$return = date($mysqlDateFormat, strtotime(trim($element->title)));		// using php's ability to GUESS a time from string representations i create a real datetime
		}
		return $return;
	}
	function parse_location($html, $feed) {
		if($feed['boro'] == 'Queens') {
			$dom = str_get_html($html['description']);
			$html = $dom->find("em",0);
			$return = trim(str_replace(',','',array_shift(explode('&nbsp;&nbsp;&nbsp;', $html->innertext))));
		} else { //if($feed['boro'] == 'Brooklyn') {
				$dom = str_get_html($html['description']);
				$element = $dom->find(".location", 0);
				$return = $element->innertext;
		}
		return $return;
	}


