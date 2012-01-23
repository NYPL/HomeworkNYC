<?php
include("../_platform.php");
include("../_core.php");
include("../Mobile_Detect.php");
?>
<html>
<head>
	<title>HomeworkNYC-Help</title>
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<?php 
	
	$detect = new Mobile_Detect();
	$mobilePlatform = '';

	if ($detect->isMobile()) {
		$mobilePlatform = 'mobile';
		mobileResources();
	}
	
	
	?>
		
	<?=js()?>
	<?=css()?>
</head>
<body>
	<div id="scroller" class="<?=$platform?> <?=$mobilePlatform?>">
	    <?php include('../_widget.header.php');?>
		<div id="mainContent">
			<h1>Help</h1>
			<h2>SearchIt!</h2>
			<div id="search-it-body" class="help-body">
				<img src="../media/gale-logo-big.png" alt="GALE" />
				<p>GALE is an e-research and educational publishing tool for libraries, schools and businesses.<br />It provides reference articles from more than 600 databases that are published online, in print, as eBooks, and in microform.</p>
				<p><strong>Activate <img id="yolink-text" src="../media/yolink-small.png" height="15"> to:</strong></p>
				<ul>
					<li class="clearfix"><img class="social-btn" src="../media/share_yo.jpg" /><span>Share the Article<br />Cite the Source with <img src="../media/easybib-icon.gif" alt="EasyBib" />EasyBib</span></li>
					<li class="clearfix"><img class="social-btn" src="../media/share_docs.jpg" /><span>Create a Google Doc of the Article</span></li>
					<li class="clearfix"><img class="social-btn" src="../media/share_tweet.jpg" /><span>Tweet the Article on Twitter</span></li>
					<li class="clearfix"><img class="social-btn" src="../media/share_face.jpg" /><span>Share the Article on Facebook</span></li>
				</ul>
			</div>
			<h2>AttendIt!</h2>
			<div id="attend-it-body" class="help-body">
				<p><strong>The Share Icons allow you to:</strong></p>
				<ul>
					<li class="clearfix"><img class="social-btn" src="../media/icon_facebook.png" /><span>Share the Event on Facebook</span></li>
					<li class="clearfix"><img class="social-btn" src="../media/icon_twitter.png" /><span>Tweet the Event on Twitter</span></li>
					<li class="clearfix"><img class="social-btn" src="../media/icon_email.png" /><span>Email the Event</span></li>
					<li class="clearfix"><img class="social-btn" src="../media/icon_google.png" /><span>Bookmark the Event through Google</span></li>
					<li class="clearfix"><img class="social-btn" src="../media/icon_addthis.png" /><span style="line-height: 16px; padding-top: 1px;">Bookmark the Event<br />Share the Event through other websites</span></li>
				</ul>
			</div>
			<h2>Dial-a-Teacher</h2>
			<div id="dial-a-teacher-body" class="help-body">
				<p>HomeworkNYC is pleased to offer live chat and whiteboard sessions with Dial-A-Teacher Online. The United Federation of Teachers and the New York City Department of Education sponsor this homework help program for students in Grades K-12.</p>
				<p>Classroom teachers are available online Monday through Thursday, from 4:30 p.m. to 6:30 p.m. on school days during most of the school year. They also answer answer homework questions by phone at <a href="tel:212-777-3380">212-777-3380</a> from 4:00 p.m.7 p.m., Monday through Thursday.</p>
				<p>The staff speaks many languages that include: Bengali, Chinese, English, French, Haitian-Creole, Russian, Slovak and Spanish. Dial-A-Teacher has a large collection of New York City school textbooks to assist students and parents with assignments.</p>
			</div>
		</div>
		<?php include('../_widget.footer.php');?>
	</div>
</body>
</html>

