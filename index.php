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
	
	echo $_SERVER['DOCUMENT_ROOT'] . "yayforimages/upload/";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	
	<title>Yay4Images</title>
	
	<link rel="stylesheet" type="text/css" href="css/base.css" />
	<link rel="stylesheet" type="text/css" href="scripts/uploadify/uploadify.css"  />
	<link rel="stylesheet" type="text/css" href="css/style.css?dummy=<?php echo rand(); ?>" />
	
	<script type="text/javascript" src="scripts/head.js"></script> <!-- from http://headjs.com/ -->
	
	<script type="text/javascript"> 
		head.js (
		'https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js',
		'scripts/ddpngfix.js',
		'scripts/qtip.js',
		'scripts/tablesorter.js',
		'scripts/uploadify/jquery.uploadify.v2.1.4.min.js',
		'scripts/uploadify/swfobject.js',
		'scripts/script.js?dummy=<?php echo rand(); ?>'
		);
	</script>

</head>

<body>
<div id="doc">
<!--	Main Nav 
======================================== -->	
	<div id="nav" class="pv">
		<ul>
			<li><a class="" href="?p=home">home</a></li>
			<li><a class="" href="?p=upload">upload</a></li>
		</ul>
		<div class="clear"></div>
		
	</div>
<div class="ph pv">


<!--	Header 
======================================== -->
	<div id="header">
		<h1><img src="assets/logo.png" alt="" /></h1>
	</div>

<!--	Content
======================================== -->	
	<div id="main">
		<?php
			switch($p) { 
				case "home":
					include('includes/home.php');
				break;
				case "upload":
					include('includes/upload.php');
				break;		
			
			}
			
	
		?>
	</div>
</body>	
</html>