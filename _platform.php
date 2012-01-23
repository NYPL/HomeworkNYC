<?php
/*
a central place to manage the determination of WHAT PLATFORM this code is running in.
This is perhaps the most tricky bit of code in the Apps/widgets

recent changes to FB api may make this cleaner.
Determining iGoogle vs Google's generic embed seems impossible (or was)

this 'myspace' code is legacy and will likely need updating this week

the BIGGEST problem is determine platform on subsequent loads... the initial load provides referer info... after the the iframe is the referer ;(

*/

if (isset($_GET['platform'])) {//echo 'get'.$_GET['platform'];
	$platform = $_GET['platform'];
	$debug = 'b';
}
else
{
	$referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
	if(stristr($referer, 'myspace')) {
		$refering_platform = 'myspace';
	} elseif(stristr($referer, 'facebook')) {
		$refering_platform = 'facebook';	
	} elseif(stristr($referer, 'google') || stristr($referer, 'gmodules')) {
		$refering_platform = 'Google';
	} elseif(stristr($referer, 'app')) {
		$refering_platform = 'app'; 		
	} elseif($referer == '' || stristr($referer, 'homeworknyc') || stristr($referer, '127.0.0.1')) {
		$refering_platform = 'HomeWorkNYC';
	} else {
		$refering_platform = 'web';
	}
	
	$platform = $refering_platform;
	$debug = 'a';
}
