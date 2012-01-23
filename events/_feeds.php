<?php
	print '<!-- _feeds.php -->';

	$refresh_time = 14400;
//	$refresh_time = 10;// uncomment this to  force cache to update on every load
	$update_cache = false;
	$feeds = array();
	// FEEDS ARE A MESS right now. boros need to be split from NYPL format needs to be standardized.
	// CORRECTION:!!! feeds are NOT a mess. One must count their blessings. And in this project there have been few!
	$feeds[] = array('boro'=>'Brooklyn',		'category'=>'Teens',	'url'=>'http://misc.brooklynpubliclibrary.org/rss/teens.xml');
	$feeds[] = array('boro'=>'Bronx',			'category'=>'Teens',	'url'=>'http://www.nypl.org/feed/rss/events/teens_bronx');
	$feeds[] = array('boro'=>'Manhattan',		'category'=>'Teens',	'url'=>'http://www.nypl.org/feed/rss/events/teens_manhattan');
	$feeds[] = array('boro'=>'Staten Island',	'category'=>'Teens',	'url'=>'http://www.nypl.org/feed/rss/events/teens_staten');
	$feeds[] = array('boro'=>'Queens',			'category'=>'Teens',	'url'=>'http://www.queenslibrary.org/rsseventsYA.rem');
	print '<!-- point 1 _feeds.php -->';
	$result = doSelect('config', 'value', "WHERE `name` = 'events_last_update'"); // get the time that feeds were pulled from their source.
	print '<!-- point A _feeds.php -->';

	$row = mysql_fetch_assoc($result);
	$cache_last_updated = $row['value'];

	print '<!-- point B _feeds.php -->';

	/*
	// query DB to get counts for each boro for the display layer
	$cached_counts = array();
	print '<!-- point C _feeds.php -->';
	$result = doSelect('event_cache', 'count(title) count, boro', 'GROUP BY boro');
	print '<!-- point D _feeds.php -->';
	while($cached_counts_row = mysql_fetch_assoc($result)) {
		$cached_counts[$cached_counts_row['boro']] = $cached_counts_row['count'];
	}
	*/

	print '<!-- point E _feeds.php -->';

	if(time()-$row['value'] > $refresh_time || $_GET['update_feeds']==true) { 			// has it been 12hours since we got fresh RSS feeds
		$update_cache = true;
	}
	print '<!-- end of _feeds.php -->';
