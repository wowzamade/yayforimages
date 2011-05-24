<?php

// Error Reporting
	ini_set('display_errors',1);
	error_reporting(E_ALL);

// Site Includes
	include('includes/config.php');

// Connect
	if(!mysqlConnect()){ echo "Error Connecting..."; }

// Page Vars	
	$p = ( isset($_GET['p']) )? $_GET['p'] : 'home';

// Passed Vars for state retention	
	$pass_vars = 'p='.$p.'';
	
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	
	<title>Title</title>
	
	<link rel="stylesheet" type="text/css" href="css/base.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css?dummy=<?php echo rand(); ?>" />
	
	<script type="text/javascript" src="scripts/head.js"></script> <!-- from http://headjs.com/ -->
	<script type="text/javascript"> 
		head.js (
		'https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js',
		'scripts/ddpngfix.js',
		'scripts/qtip.js',
		'scripts/tablesorter.js',
		'scripts/script.js?dummy=<?php echo rand(); ?>'
		);
	</script>
	
</head>

<body>

<div id="doc">
<div class="ph_double pv_double">


<!--	Header 
======================================== -->
	<div id="header">
		<h1>This is the header</h1>
	</div>


<!--	Main Nav 
======================================== -->	
	<div id="nav" class="pv">
		
		<span class="light_text">Navigation:</span>
		
		<ul>
			<li><a class="" href="#">home</a></li>
			<li><a class="" href="#">second nav item</a></li>
			<li><a class="" href="#">third nav item</a></li>
		</ul>
		<div class="clear"></div>
		
	</div>


<!--	Content
======================================== -->	
	<div id="main">
		
		<h1>Header h1</h1>
		<p>Lorem ipsum dolor. <span class="light_text">This is some text with the class "light_text"</span>, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.</p>

		<h1 class="italic">A very very very very very very "italics" and long header h1</h1>
		<p>Duis autem vel eum iriure <span class="light_text">This is some text with the class "light_text"</span> dolor in hendrerit <a href="#">in vulputate velit esse molestie</a> consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
		<p>Ut wisi enim ad minim veniam, quis nostrud <strong>This is some text in a "strong" tag</strong> exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu <a href="#">in vulputate velit esse molestie</a> feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
		
		<h1>Png test</h1>
		<p class="pad"><img class="png" alt="" src="assets/test.png" /></p>
		
		
		<div class="margin pad split_top split_bottom">
			
			<h1>Table with "tablesorter", "borders" and "cell_padding"</h1>
			<table class="tablesorter borders cell_padding">
				<thead>
					<tr>
						<th>Column One</th>
						<th>Column Two</th>
						<th>Column Three</th>
						<th>Column Four</th>
					</tr>
				</thead>
				<tbody>
					<tr class="odd">
						<td>12341234</td>
						<td>vulputate velit</td>
						<td>*</td>
						<td>4 5 6 7</td>
					</tr>
					<tr class="even">
						<td>253452345</td>
						<td>axerci tation ullamcorper</td>
						<td>*</td>
						<td>1 2 3 4</td>
					</tr>
					<tr class="odd">
						<td>634534</td>
						<td>esse molestie</td>
						<td>*</td>
						<td>9 7 5 4</td>
					</tr>				
				</tbody>
				<tfoot>
					<tr>
						<td colspan="1000">Footer</td>
					</tr>
				</tfoot>
			</table>
			
		</div>
				
		<h2>Header h2</h2>
		<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, <a href="#">in vulputate velit esse molestie</a> sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>

		<h2>A very very very very very very long header h2</h2>
		<p>Duis autem vel eum iriure <span class="light_text">This is some text with the class "light_text"</span> dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
		<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>

		<h3>Header h3</h3>
		<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam <span class="light_text">This is some text with the class "light_text"</span> nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>

	</div>
	
	<div id="footer">
		<h3>Footer</h3>
		<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
		<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>	
	</div>
	
</div>		
</div>
</body>	
</html>