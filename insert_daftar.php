<?php
session_start();	
require("config.php");

$cib         = $_SESSION['id']; 
$dokter      = $_SESSION['dokter'];
$tgl         = invertDate($_SESSION['tanggal']);
$jam         = $_SESSION['jam'];
$index_hari  = $_SESSION['index_hari']; 
$poli        = $_SESSION['poli']; 
$nama_pasien = $_SESSION['nama_pasien']; 
$no_hp       = $_SESSION['no_hp']; 

$result = $koneksi -> query("select * from antrian_online_format_inc where tgl = '$tgl' and jam = '$jam' and dokter = '$dokter' and cib = '$cib' and poli = '$poli' LIMIT 1");
if($row = $result -> fetch_array()){ 
	$_SESSION['no_online'] = $row['no_online'];
	$_SESSION['kode_booking'] = $row['kode_booking'];	
	$kode_booking = $row['kode_booking'];
	$no_online =  $row['no_online'];
	header('location:cetak.php');
        exit();
}



$no_online = 1; ///start nomor
$query = $koneksi -> query("select * from antrian_online_format_inc where no_online >= '$no_online' and dokter = '$dokter' and tgl = '$tgl' and jam = '$jam' ORDER BY no_online DESC LIMIT 1");
while($row = $query -> fetch_array()){								
	$no_online = $row['no_online'] + 1;
}

$no_kartu = '';
$query = $koneksi -> query("select * from pasien_pribadi where id = '$cib' LIMIT 1");
while($row = $query -> fetch_array()){								
	$no_kartu = $row['no_kartu_bpjs'];
}



$kode_booking = sprintf("%07d", mt_rand(1000000, 9999999));
$query = mysqli_query($koneksi, "INSERT INTO antrian_online_format_inc values ('$dokter', '$tgl', '$jam', '$no_online', '$cib', '$index_hari', '$poli' , '$kode_booking', '$no_kartu', '', '', now(), '', '' )");


$isBerhasilMasuk = false;
$query = $koneksi -> query("select * from antrian_online_format_inc where kode_booking = '$kode_booking' and cib = '$cib' and tgl = '$tgl' LIMIT 1");
if($row = $query -> fetch_array()){ $isBerhasilMasuk = true; }

if($isBerhasilMasuk==true){
  setcookie('cookie_kode_booking', $kode_booking, time() + (86400 * 3), "/"); //86400 = 1 day
  $_SESSION['no_online'] = $no_online; 
  $_SESSION['kode_booking'] = $kode_booking; 
  header('location:cetak.php');
}else{
  header('location:login.php?m=error_simpan_data');
  exit();
}



?>
