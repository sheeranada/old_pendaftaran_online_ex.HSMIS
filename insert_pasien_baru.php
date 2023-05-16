<?php

session_start();	
require('config.php');


$t1 = $_POST['T1'];
$t2 = $_POST['T2'];
$t3 = $_POST['T3'];
$t4 = $_POST['T4'];
$t5 = '';
if(strlen($_POST['T5'])>0) { $t5 = $_POST['T5']; }
$t6 = $_POST['T6'];
$t7 = $_POST['T7'];
$t10 = $_POST['T10'];

if(strlen($t7)==0){
	header("location:form_pasien_baru.php?m=ktp");
	exit();
}else{

  ///pertama cek no KTPnya, jika sudah ada langsung masuk ke kode booking
  $result = $koneksi -> query("select * from pasien_baru, antrian_online_format_inc where id = cib and no_ktp = '".$t7."' order by tgl DESC LIMIT 1");
  if($rowx = $result -> fetch_array()){	
		$_SESSION['id'] = $rowx['id'];
		$_SESSION['nama_pasien'] = $rowx['nama'];
		$_SESSION['poli'] = $rowx['poli'];
		$_SESSION['dokter'] = $rowx['dokter'];
		$_SESSION['index_hari'] = $rowx['index_hari'];
		$_SESSION['tanggal'] = invertDate($rowx['tgl']);
		$_SESSION['jam'] = $rowx['jam'];
		$_SESSION['no_online'] = $rowx['no_online'];
		$_SESSION['kode_booking'] = $rowx['kode_booking'];	
		$_SESSION['no_hp'] = $rowx['telp'];			
	    header('location:cetak.php');							
	    exit();
  }
} 

$_SESSION['t1'] = $_POST['T1'];
$_SESSION['t2'] = $_POST['T2'];
$_SESSION['t3'] = $_POST['T3'];
$_SESSION['t4'] = $_POST['T4'];
$_SESSION['t5'] = $_POST['T5'];
$_SESSION['t6'] = $_POST['T6'];
$_SESSION['t7'] = $_POST['T7'];
$_SESSION['t8'] = $_POST['T8'];
$_SESSION['t10'] = $_POST['T10'];
$captcha = $_POST['T11'];

if($captcha!=$_SESSION['captcha']){
	header("location:form_pasien_baru.php?m=captcha");
	exit();
}else if(strlen($captcha)==0){
	header("location:form_pasien_baru.php?m=captcha");
	exit();
}else if(strlen($t1)==0){
	header("location:form_pasien_baru.php?m=n");
	exit();
}else if(strlen($t2)==0){
	header("location:form_pasien_baru.php?m=a");
	exit();
}else if(strlen($t10)==0||$t10=='PILIH KOTA'){
	header("location:form_pasien_baru.php?m=kota");
	exit();
}else if(strlen($t3)==0||$t3=='PILIH KECAMATAN'){
	header("location:form_pasien_baru.php?m=kec");
	exit();
}else if(strlen($t4)==0||$t4=='PILIH KELURAHAN'){
	header("location:form_pasien_baru.php?m=kel");
	exit();
}else if(strlen($t5)==0){
	header("location:form_pasien_baru.php?m=tgl");
	exit();
}

//// Opsi A) klo pake WA, diredirect ke form_no_telp (perhatikan jumlah token yg dimiliki) /////
////header("location:form_no_telp.php");

//// Opsi B) klo gak punya token WA langsung di save_pasien_baru //////
header('location:save_pasien_baru.php');	


exit();
?>
