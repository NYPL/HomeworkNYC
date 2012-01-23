
<div id="widget_nav_bar">
	<!-- <a href="http://HomeworkNYCbeta.org" target="_new"><img src="/widgets/media/powered-by-homework-nyc-small.gif" width="230" height="16" alt="Powered By HomeworkNyc"></a> -->
	<?php if($platform == 'facebook'): ?>
			<br />
			<a target="_top" href="http://apps.facebook.com/homeworknycsearch/">SearchIt</a> - 
			<a target="_top" href="http://apps.facebook.com/homeworknycevents/">AttendIt</a> - 
			<a target="_top" href="http://apps.facebook.com/homeworknyclist/">ListIt</a>
	<?php endif;?>

</div>	

<script type="text/javascript" charset="utf-8">
	$('#developer_info').click(function() {
		$('#developer_info pre').toggle();
	})
</script>
<div id="privacy-stmt"><a style="font-size:.8em" target="_top" href="http://homeworknycbeta.org/privacy">Privacy Statement</a> </div>
<div id="developer_info">.
<pre><?
//print "uid:{$nypl_user_id}:{$_COOKIE['google_id']}\n";
//print "{$platform}:{$_COOKIE['platform']}:{$refering_platform}\n";
//print client_ip()."\n";
//print $debug;
//print_r($_SERVER);
?>
</pre>
</div>
</div><!-- wrapper -->
