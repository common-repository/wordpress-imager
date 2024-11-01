<?php
header('Content-Type: text/css');
?>
#lbOverlay { position: fixed; top: 0; left: 0; z-index: 99998; width: 100%; height: 500px; }
#lbOuterContainer { position: relative; background-color: #fff; width: 200px; height: 200px; margin: 0 auto; }
#lbMain { position: absolute; left: 0; width: 100%; z-index: 99999; text-align: center; line-height: 0; }
#lbMain a img { border: none; }
#lbDetailsContainer {	font: 10px Verdana, Helvetica, sans-serif; background-color: #fff; width: 100%; line-height: 1.4em;	overflow: auto; margin: 0 auto; }
#lbImageContainer, #lbIframeContainer { padding: 10px; }
#lbLoading {
	position: absolute; top: 45%; left: 0%; height: 32px; width: 100%; text-align: center; line-height: 0; background: url(images/loading.gif) center no-repeat;
}
#lbHoverNav { position: absolute; top: 0; left: 0; height: 100%; width: 100%; z-index: 10; }
#lbImageContainer>#lbHoverNav { left: 0; }
#lbHoverNav a { outline: none; }
#lbPrev { width: 49%; height: 100%; background: transparent url(images/blank.gif) no-repeat; display: block; left: 0; float: left; }
#lbNext { width: 49%; height: 100%; background: transparent url(images/blank.gif) no-repeat; display: block; right: 0; float: right; }
#lbPrev2, #lbNext2 { text-decoration: none; font-weight: bold; }
#lbPrev2_Off, #lbNext2_Off { font-weight: bold; }
#lbDetailsData { padding: 0 10px; }
#lbDetails { width: 60%; float: left; text-align: left; }
#lbCaption { display: block; font-weight: bold; }
#lbCaption a {color:black;}
#lbNumberDisplay { float: left; display: block; padding-bottom: 1.0em; }
#lbNavDisplay { float: left; display: block; padding-bottom: 1.0em; }
#lbClose { width: 64px; height: 28px; float: right; margin-bottom: 1px; }
#lbPlay { width: 64px; height: 28px; float: right; margin-bottom: 1px; }
#lbPause { width: 64px; height: 28px; float: right; margin-bottom: 1px; }

<?php
if($_GET['ver'] == 'grey'){ ?>
	#lbOverlay { background-color: #000000; }
	#lbOuterContainer { border: 3px solid #888888; }
	#lbDetailsContainer { border: 3px solid #888888; border-top: none; }
	#lbPrev:hover, #lbPrev:visited:hover { background: url(images/prev_grey.gif) left 15% no-repeat; }
	#lbNext:hover, #lbNext:visited:hover { background: url(images/next_grey.gif) right 15% no-repeat; }
	#lbPrev2, #lbNext2, #lbSpacer { color: #333333; }
	#lbPrev2_Off, #lbNext2_Off { color: #CCCCCC; }
	#lbDetailsData { color: #333333; }
	#lbClose { background: url(images/close_grey.png) no-repeat; }
	#lbPlay { background: url(images/play_grey.png) no-repeat; }
	#lbPause { background: url(images/pause_grey.png) no-repeat; }
<?php } elseif($_GET['ver'] == 'red') { ?>
	#lbOverlay { background-color: #330000; }
	#lbOuterContainer { border: 3px solid #DD0000; }
	#lbDetailsContainer { border: 3px solid #DD0000; border-top: none; }
	#lbPrev:hover, #lbPrev:visited:hover { background: url(images/prev_red.gif) left 15% no-repeat; }
	#lbNext:hover, #lbNext:visited:hover { background: url(images/next_red.gif) right 15% no-repeat; }
	#lbPrev2, #lbNext2, #lbSpacer { color: #620000; }
	#lbPrev2_Off, #lbNext2_Off { color: #FFCCCC; }
	#lbDetailsData { color: #620000; }
	#lbClose { background: url(images/close_red.png) no-repeat; }
	#lbPlay { background: url(images/play_red.png) no-repeat; }
	#lbPause { background: url(images/pause_red.png) no-repeat; }
<?php } elseif($_GET['ver'] == 'green') { ?>
	#lbOverlay { background-color: #003300; }
	#lbOuterContainer { border: 3px solid #00B000; }
	#lbDetailsContainer { border: 3px solid #00B000; border-top: none; }
	#lbPrev:hover, #lbPrev:visited:hover { background: url(images/prev_green.gif) left 15% no-repeat; }
	#lbNext:hover, #lbNext:visited:hover { background: url(images/next_green.gif) right 15% no-repeat; }
	#lbPrev2, #lbNext2, #lbSpacer { color: #003300; }
	#lbPrev2_Off, #lbNext2_Off { color: #82FF82; }
	#lbDetailsData { color: #003300; }
	#lbClose { background: url(images/close_green.png) no-repeat; }
	#lbPlay { background: url(images/play_green.png) no-repeat; }
	#lbPause { background: url(images/pause_green.png) no-repeat; }
<?php } elseif($_GET['ver'] == 'blue') { ?>
	#lbOverlay { background-color: #011D50; }
	#lbOuterContainer { border: 3px solid #5F89D8; }
	#lbDetailsContainer { border: 3px solid #5F89D8; border-top: none; }
	#lbPrev:hover, #lbPrev:visited:hover { background: url(images/prev_blue.gif) left 15% no-repeat; }
	#lbNext:hover, #lbNext:visited:hover { background: url(images/next_blue.gif) right 15% no-repeat; }
	#lbPrev2, #lbNext2, #lbSpacer { color: #01379E; }
	#lbPrev2_Off, #lbNext2_Off { color: #B7CAEE; }
	#lbDetailsData { color: #01379E; }
	#lbClose { background: url(images/close_blue.png) no-repeat; }
	#lbPlay { background: url(images/play_blue.png) no-repeat; }
	#lbPause { background: url(images/pause_blue.png) no-repeat; }
<?php } elseif($_GET['ver'] == 'gold') { ?>
	#lbOverlay { background-color: #666600; }
	#lbOuterContainer { border: 3px solid #B0B000; }
	#lbDetailsContainer { border: 3px solid #B0B000; border-top: none; }
	#lbPrev:hover, #lbPrev:visited:hover { background: url(images/prev_gold.gif) left 15% no-repeat; }
	#lbNext:hover, #lbNext:visited:hover { background: url(images/next_gold.gif) right 15% no-repeat; }
	#lbPrev2, #lbNext2, #lbSpacer { color: #666600; }
	#lbPrev2_Off, #lbNext2_Off { color: #E1E100; }
	#lbDetailsData { color: #666600; }
	#lbClose { background: url(images/close_gold.png) no-repeat; }
	#lbPlay { background: url(images/play_gold.png) no-repeat; }
	#lbPause { background: url(images/pause_gold.png) no-repeat; }
<?php } elseif($_GET['ver'] == 'orange') { ?>
	#lbOverlay { background: #9F5600; }
	#lbOuterContainer { border: 3px solid #BF810B; }
	#lbDetailsContainer { border: 3px solid #BF810B; border-top: none; }
	#lbPrev:hover, #lbPrev:visited:hover { background: url(images/prev_gold.gif) left 15% no-repeat; }
	#lbNext:hover, #lbNext:visited:hover { background: url(images/next_gold.gif) right 15% no-repeat; }
	#lbPrev2, #lbNext2, #lbSpacer { color: #666600; }
	#lbPrev2_Off, #lbNext2_Off { color: #E1E100; }
	#lbDetailsData { color: #666600; }
	#lbClose { background: url(images/close_gold.png) no-repeat; }
	#lbPlay { background: url(images/play_gold.png) no-repeat; }
	#lbPause { background: url(images/pause_gold.png) no-repeat; }
<?php } ?>