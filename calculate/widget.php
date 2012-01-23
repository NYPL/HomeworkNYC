<?php
include("../_platform.php");
include("../_core.php");
include("../Mobile_Detect.php");
?>
<html>
<head>
	<title>HomeworkNYC-CalculateIt!</title>
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
			<h1>CalculateIt!</h1>
			<div class="calculate-body">
				<p>These calculators cover different subjects and work in different ways. Choose the one you like best.</p>
				<h3><strong><a target="_blank" href="http://www.algebrahelp.com/calculators/">Algebrahelp.com Calculators</a></strong></h3>
				<ul>
					<li><strong>Equations:</strong> Solving, Simplifying, Solve by Factoring, Completing the Square, Graphing, 3D Graphing, Substitution</li>
					<li><strong>Expressions:</strong> Simplifying, Combining, Factoring, Substitution, Evaluating Like Terms</li>
					<li><strong>Functions:</strong> Graphing</li>
					<li><strong>Fractions:</strong> Factoring, Prime Factoring, Percentages</li>
					<li><strong>Numbers:</strong> Simplifying, Addition, Subtraction, Multiplication, Division, Comparison</li>
					<li><strong>Other:</strong> Proportions, Order of Operations</li>
				</ul>
				<h3><strong><a target="_blank" href="http://www.quickmath.com/">Quickmath.com Calculators</a></strong></h3>
				<ul>
					<li><strong>Algebra:</strong> Expand, Factor, Simplify, Cancel, Partial Fractions, Joint Fractions</li>
					<li><strong>Equations:</strong> Solve, Plot, Quadratics</li>
					<li><strong>Inequalities:</strong> Solve, Plot</li>
					<li><strong>Calculus:</strong> Differentiate, Integrate</li>
					<li><strong>Matrices:</strong> Arithmetic, Inverse, Determinant</li>
					<li><strong>Graphs:</strong> Equations, Inequalities</li>
					<li><strong>Numbers:</strong> Percentages, Scientific notation</li>
				</ul>
			</div>
		</div>
		<?php include('../_widget.footer.php');?>
	</div>
</body>
</html>

