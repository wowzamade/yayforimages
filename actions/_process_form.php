<?php

	ini_set('display_errors',1);
	error_reporting(E_ALL);

// Site Includes
	include('../includes/config.php');

// Connect
	if(!mysqlConnect()){ echo "Error Connecting..."; }
	
	
	
	
	$var = (isset($_POST['var']))? $_POST['var'] : '';	
	
	
	
?>