<?php

if(isset($_GET['dokter']) && !empty($_GET['dokter'])) { 
	session_start();	
	$_SESSION['dokter'] = $_GET['dokter']; 
	header('location:pilih_hari.php');
}else{
	header('location:pilih_dokter.php');
} 


?>
