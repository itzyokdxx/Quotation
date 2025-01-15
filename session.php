<?php 

	include('includes/config.php');

	// Connect to the database
	$mysqli = new mysqli('localhost:3307', 'root', '', 'invoicemgsys');

	session_start();

	$check = $_SESSION['login_username'];

	if(!isset($check)) {
	    header("Location:index.php");
	}

?>