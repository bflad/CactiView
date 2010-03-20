<?

#########################################################################
#
#       CactiView v1.0 - Brian Flad
#       http://fladpad.com - bflad417@gmail.com
#       
#       Forked from the excellent:
#       CactiView v0.1 - Laurie Denness
#       http://laurie.denness.net - laurie@denness.net
#
#       Displays a section of Cacti graphs based on your selection.
#       Graphs rotate automatically rotate with AJAX
#
#       Configuration is available in config.php
#
#########################################################################

require_once("config.php");

header("Cache-Control: no-cache, must-revalidate");	
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$graphs_count = count($graphs);

$window_height = $_GET['height'];
$window_width = $_GET['width'];
if ($window_height == null || $window_width == null) { ?>
<html>
	<head>
		<title>CactiView -- Auto Sizing</title>
		<script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
		<script>
			$(document).ready(function() {
				$('#sizeform').submit();
			});
		</script>
	</head>
	<body>
		<form action="" id="sizeform">
			<input type="hidden" id="height" name="height" value="" />
			<input type="hidden" id="width" name="width" value="" />
			<input type="submit" />
		</form>
		<script>
			$('#height').val($(window).height());
			$('#width').val($(window).width());
		</script>
	</body>
</html>
<? 
	exit;
} 
$left_graph_div_height = floor($window_height) - 5;
$left_graph_div_width = floor(($window_width) * 0.75) - 8;
$right_graph_div_height = floor(($window_height) * 0.30) - 5;
$right_graph_div_width = floor(($window_width) * 0.25) - 8;
$timestamp_div_height = floor(($window_height) * 0.1) - 5;
$timestamp_div_width = $right_graph_div_width;
$timestamp_font_size = floor($timestamp_div_height * 0.40);
$current_title_font_size = floor($window_height * 0.065);
$next_title_font_size = floor($current_title_font_size * 0.50);
?>
<html>
    <head>
	    <meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
		<title>CactiView</title>
		<script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
		<script language="javascript">
			var graphs = new Array();
			<? foreach ($graphs as $index=>$graph) { ?>
				graphs[<?= $index ?>] = new Array();
				graphs[<?= $index ?>]['cactiid'] = "<?= $graph['cactiid'] ?>";
				graphs[<?= $index ?>]['title'] = "<?= $graph['title'] ?>";
			<? } ?>
			var url = "<?= $cactipath ?>graph_image.php?";
			var left_graph_height = <?= ($left_graph_div_height - 70) ?>;
			var left_graph_width = <?= ($left_graph_div_width - 105) ?>;
			var right_graph_height = <?= ($right_graph_div_height - 70) ?>;
			var right_graph_width = <?= ($right_graph_div_width - 105) ?>;
			var aTimer;
			var current_index = 0;
			var next_index = 1;
			function timestamp() {
				var now = new Date();
				var months = new Array(13);
				months[0]  = "January";
				months[1]  = "February";
				months[2]  = "March";
				months[3]  = "April";
				months[4]  = "May";
				months[5]  = "June";
				months[6]  = "July";
				months[7]  = "August";
				months[8]  = "September";
				months[9]  = "October";
				months[10] = "November";
				months[11] = "December";
				var year = now.getFullYear();
				var monthname = months[now.getMonth()];
				var monthday = now.getDate();
				var hour = now.getHours();
				var minute = now.getMinutes();
				var second = now.getSeconds();
				var ap = "AM";
				if (hour   > 11) { ap = "PM";             }
				if (hour   > 12) { hour = hour - 12;      }
				if (hour   == 0) { hour = 12;             }
				if (hour   < 10) { hour   = "0" + hour;   }
				if (minute < 10) { minute = "0" + minute; }
				if (second < 10) { second = "0" + second; }
				return monthname + ' ' + monthday + ', ' + year + "<br />" +
					hour + ':' + minute + ':' + second + " " + ap;
			}
			function startTimer() {
				nextGraph();
				aTimer = window.setInterval("nextGraph();",<?= $timeout ?>);
				return false;
			}
			function urlLeft(current_index) {
				return url+
					'action=view'+
					'&local_graph_id='+graphs[current_index]['cactiid']+
					'&rra_id=0'+
					'&graph_height='+left_graph_height+
					'&graph_width='+left_graph_width+
					'&graph_nolegend=1';
			}
			function urlRight(current_index) {
				return url+
					'action=view'+
					'&local_graph_id='+graphs[current_index]['cactiid']+
					'&graph_height='+right_graph_height+
					'&graph_width='+right_graph_width+
					'&graph_nolegend=1';
			}
			function urlRightTop(current_index) {
				return urlRight(current_index)+'&rra_id=2';
			}
			function urlRightMiddle(current_index) {
				return urlRight(current_index)+'&rra_id=3';
			}
			function urlRightBottom(current_index) {
				return urlRight(current_index)+'&rra_id=4';
			}
			function loadLeftGraph(current_index) {
				var img = new Image();
				$(img).load(function () {
					$(this).hide();
					$('#left-graph').removeClass('loading').html(this);
					$(this).fadeIn();
				}).error(function () {
					// notify the user that the image could not be loaded
					alert("Error loading image!");
				}).attr('src', urlLeft(current_index));
			}
			function loadRightTopGraph(current_index) {
				var img = new Image();
				$(img).load(function () {
					$(this).hide();
					$('#right-graph-top').removeClass('loading').html(this);
					$(this).fadeIn();
				}).error(function () {
					// notify the user that the image could not be loaded
					alert("Error loading image!");
				}).attr('src', urlRightTop(current_index));
			}
			function loadRightMiddleGraph(current_index) {
				var img = new Image();
				$(img).load(function () {
					$(this).hide();
					$('#right-graph-middle').removeClass('loading').html(this);
					$(this).fadeIn();
				}).error(function () {
					// notify the user that the image could not be loaded
					alert("Error loading image!");
				}).attr('src', urlRightMiddle(current_index));
			}
			function loadRightBottomGraph(current_index) {
				var img = new Image();
				$(img).load(function () {
					$(this).hide();
					$('#right-graph-bottom').removeClass('loading').html(this);
					$(this).fadeIn();
				}).error(function () {
					// notify the user that the image could not be loaded
					alert("Error loading image!");
				}).attr('src', urlRightBottom(current_index));
			}
			function nextGraph() {
				if (current_index==<?= $graphs_count ?>) {
					current_index = 0;
				}
				if (next_index==<?= $graphs_count ?>) {
					next_index = 0;
				}
				$('#current_title').text(graphs[current_index]['title']);
				$('#next_title').text("Next: "+graphs[next_index]['title']);
				$('#timestamp').html(timestamp());
				$('.left-graph').addClass('loading').html("");
				$('.right-graph').addClass('loading').html("");
				
				loadLeftGraph(current_index);
				loadRightTopGraph(current_index);
				loadRightMiddleGraph(current_index);
				loadRightBottomGraph(current_index);
				
				current_index++;
				next_index++;
			}
		</script>
		<style>
			* {
				font-family: arial, georgia, sans-serif;
				padding: 0px;
				margin: 0px;
			}
			div.left-graph {
				float: left;
				height: <?= $left_graph_div_height ?>;
				width: <?= $left_graph_div_width ?>;
			}
			div.right-graph {
				float: right;
				height: <?= $right_graph_div_height ?>;
				width: <?= $right_graph_div_width ?>;
			}
			.loading {
				background: url(ajax-loader.gif) no-repeat center center;
			}
			#content {
				clear: both;
			}
			#current_title {
				font-size: <?= $current_title_font_size ?>px;
				font-weight: bold;
			}
			
			#next_title {
				font-size: <?= $next_title_font_size ?>px;
				font-style: italic;
			}
			#timestamp {
				float: right;
				font-size: <?= $timestamp_font_size ?>px;
				font-weight: bold;
				height: <?= $timestamp_div_height ?>;
				text-align: center;
				width: <?= $timestamp_div_width ?>;
			}
			#titles {
				left: 75px;
				position: absolute;
				top: 25px;
			}
		</style>
	</head>
	<body>
		<span id="titles"><span id="current_title"></span><br /><span id="next_title"></span></span>
		<div id="content">
			<div class="left-graph loading" id="left-graph"></div>
			<div class="right-graph loading" id="right-graph-top"></div>
			<div class="right-graph loading" id="right-graph-middle"></div>
			<div class="right-graph loading" id="right-graph-bottom"></div>
			<div id="timestamp"></div>
		</div>
		<script>
			startTimer();
		</script>
	</body>
</html>