<?php


if(isset($_GET['no']) && !empty($_GET['no'])) { 
	session_start();	
	$_SESSION['no'] = $_GET['no']; 
}else{
	header('location:pilih_jam.php');
	exit();
} 


if(isset($_GET['jam_nomor']) && !empty($_GET['jam_nomor'])) { 
	session_start();	
	$_SESSION['jam_nomor'] = $_GET['jam_nomor']; 
}else{
	header('location:pilih_jam.php');
	exit();
} 


header('location:konfirmasi.php');


?>
