<?php 
/**
 * DOC COMMENT 
 * 
 * PHP Version 7
 * 
 * @category   Document
 * @package    Flatnet2
 * @subpackage NONE
 * @author     Steven Schödel <steven.schoedel@outlook.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link       none
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Chart.js demo</title>
        <script src='Chart.min.js'></script>
    </head>
     <body>
     
     <canvas id="buyers" width="900" height="200"></canvas>
<?php
echo '
<script>
var buyerData = {
	labels : ["January","February","March","April","May","June"],
	datasets : [
	{
		fillColor : "rgba(172,194,132,0.4)",
		strokeColor : "#ACC26D",
		pointColor : "#fff",
		pointStrokeColor : "#9DB86D",
		data : [203,156,99,251,305,247]
	}
	]
}
 
var buyers = document.getElementById(\'buyers\').getContext(\'2d\');
	    new Chart(buyers).Line(buyerData);
	</script>
		';
?>
</body>

</html>