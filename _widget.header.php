
<?php 
	/* hide navigation when page is being pulled from a mobile app
	 * mobile has built-in nav already
	*/
	if ($platform != "app") {
?>

<div id="header">
	<div id="hwnyc-logo">
		<span class="ir">Homework NYC</span>
	</div>
</div>

<div class="tabs">
	<span id="searchit" class="tab"><a href='/widgets/search/widget.php?platform=<?=$platform?>'><span class="select-arrow">SearchIt!</span></a></span>
	<span id="attendid" class="tab"><a href='/widgets/events/widget.php?platform=<?=$platform?>'><span class="select-arrow">AttendIt!</span></a></span>
	<span id="calculate" class="tab"><a href='/widgets/calculate/widget.php?platform=<?=$platform?>'><span class="select-arrow">CalculateIt!</span></a></span>
	<span id="dial" class="tab"><a href='/widgets/dialateacher/widget.php?platform=<?=$platform?>'><span class="select-arrow">Dial-a-Teacher</span></a></span>
	<span id="help" class="tab"><a href='/widgets/help/widget.php?platform=<?=$platform?>'><span class="select-arrow">Help</span></a></span>
</div>
<?php
	}
	
	else if ($platform == "app") {
?>
	<style>
		#search-popuphelp {
			top: 79px !important;
		}
	</style>
<?php
	}
?>
<div id="wrapper">
<?php 
	/* also, disable any extraneous scripting when pulling from a mobile app
	*/
	if ($platform != "app") {
?>
<script type="text/javascript">

$(document).ready(function() {

    $('div.tabs span').removeClass('selected');
	var url=window.location+"";//conver to string
	
	var pos = url.indexOf("search");
    if(pos!=-1){
	   $('#searchit').addClass('selected');
	   $('body').addClass('ruled-BG');
	}
	pos = url.indexOf("events");
	if(pos!=-1){
	   $('#attendid').addClass('selected');
	    
	}

	pos = url.indexOf("calculate");
	if(pos!=-1) {
	   $('#calculate').addClass('selected');
	   $('body').addClass('ruled-BG');
	}
	
	pos = url.indexOf("dialateacher");
	if(pos!=-1){
	   $('#dial').addClass('selected');

	}
	pos = url.indexOf("help");
	if(pos!=-1) {
	   $('#help').addClass('selected');
	   $('body').addClass('steel-BG');
	}

  })
	
</script>
<?php
	}
?>