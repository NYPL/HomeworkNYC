<?php
//print print_r($_SERVER);	
//error_reporting(E_ERROR);
	
	$CFG_site_url = 'http://homeworknycbeta.org/';
	
	date_default_timezone_set('America/New_York');
	
	function pre($s) {
		print '<pre>'.print_r($s, true).'</pre>';
	}
	
	function js($exceptions=array()) {
		// centrally manage the JS files to include in the header, and how they are included
		$js = array(
//			'lib/jquery-1.3.2.min.js',
			'lib/jquery-1.4.2.min.js',
			'lib/jquery.autocomplete.js',
			'lib/jquery.cookie.js',
			//'lib/phonegap-1.1.0.js',
			'_widget.js'
		);
		foreach($js as $j) {
			print '<script src="/widgets/'.$j.'" type="text/javascript" charset="utf-8"></script>'."\n";
		}
	}
	function css() {
		// centrally manage CSS ( see js() )
		$css = array(
			'lib/jquery.autocomplete.css',
			'_widget.css',
			'reset.css',
		);
		foreach($css as $s) {
			print '<link rel="stylesheet" type="text/css" href="/widgets/'.$s.'" />'."\n";
		}
		
		
	}
	
	function mobileResources() {
		// output any mobile specific resources 
		
		print '<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1,minimum-scale=1" />'."\n";
	
	}
	
	function doLog($log, $value) {
		// $log [string] = name of log to append $value to.
		// date string is automaticlly added
		// log location should NOT be hardcoded here.
		$log = $_SERVER['DOCUMENT_ROOT'].'/widgets_logs/'.$log.date("Ymd").'.log';
		$f = fopen($log, 'a+');
		fwrite($f, date("Ymd-H:i:s")."	".$value."\n");
	}
	
	function escape_for_css($str) {
		// simple regex to simplify a $str[string] to use only css selector capable characters.
		return preg_replace("/[^a-zA-Z0-9\s]/", '', $str);
	}

	function div($class='', $id='', $value='') {
		//html helper function, abstracting HTML for use in PHP code.
		return h($value, 'div', array('class'=>$class, 'id'=>$id));
	}
	function h($content, $tag, $attr=array()) {
		//html helper function, abstracting HTML for use in PHP code.
		// see div() this is the beginning of a more generic approach.
		$attr_string = '';
		foreach($attr as $k=>$v) {
			$attr_string .= "{$k}='{$v}'";
		}
		return "\n<{$tag} {$attr_string}>{$content}</{$tag}>";
	}
	
	function client_ip() {
		// not all headers are equal... this tries a few before returning what is hopefully a good client IP
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))    {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    } else {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}
	
	function paginate($total, $curr_page, $items_per_page) {
		// just like it sounds. 
		// no HTML link, this is part of a javascript inline/ajax pagination
		print '<div id="paginate">';
		if($curr_page > 1) {
			print " <span style='text-decoration:underline;cursor:pointer;' class='page_nav' id='prev'>Previous Page</span> | ";
		}
		print " Page <span id='current_page'>{$curr_page}</span> of {$total} Total Results ";
		if(($items_per_page*$curr_page) < $total) {
			print " | <span style='text-decoration:underline;cursor:pointer;' class='page_nav' id='next'>Next Page</span>";
		}
		print '</div>';
	}