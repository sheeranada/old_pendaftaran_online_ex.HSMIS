<?php

session_start();
require("config.php");	

if(isset($_GET['jenis']) && !empty($_GET['jenis'])) { 
	$_SESSION['jenis'] = $_GET['jenis']; 
	header('location:pilih_poli.php');
}else{
	header('location:pilih_jenis.php');
} 



?>
