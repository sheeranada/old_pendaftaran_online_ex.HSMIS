<?php


session_start();
require("config.php");	


if(isset($_GET['poli']) && !empty($_GET['poli'])) { 
	$_SESSION['poli'] = $_GET['poli']; 

	$result = $koneksi -> query("select * from antrian_online_format_inc where tgl >= curdate() and poli = '".$_SESSION['poli']."' and cib = '".$_SESSION['id']."' LIMIT 1");
	if($row = $result -> fetch_array()){ 
	
		session_start();	
		$_SESSION['dokter'] = $row['dokter'];		
		$_SESSION['index_hari'] = $row['index_hari']; 
		$_SESSION['tanggal'] =  invertDate($row['tgl']);		
		$_SESSION['no_online'] = $row['no_online'];
		$_SESSION['jam'] = $row['jam'];		
		$_SESSION['kode_booking'] = $row['kode_booking'];	
		header('location:cetak.php');
	}else{
		header('location:pilih_dokter.php');
	}
}else{
	header('location:pilih_poli.php');
} 



?>
