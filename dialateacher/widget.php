<?php
include("../_platform.php");
include("../_core.php");
?>
<html>
<head>
	<title>HomeworkNYC - Dial A Teacher</title>
	<?=js()?>
	<?=css()?>
	
</head>
<body>
	<?php
		
		
		if ($platform == "facebook") { $meeboWidth = "700"; }
		else { $meeboWidth = "300"; }
	?>
	
	<div id="scroller" class="<?=$platform?>">
	    <?php include('../_widget.header.php');?>
			<div align="center" style="padding-top: 20px;">
				<embed src="http://widget.meebo.com/mm.swf?OBoFSVwPZq" type="application/x-shockwave-flash" width="<?=$meeboWidth?>" height="400"></embed>
		    </div>
		<?php include('../_widget.footer.php');?>
	</div>
</body>
</html>

