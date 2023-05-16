<?php

if(!isset($_POST['T1']) && empty($_POST['T1'])) { 
    header('location:login.php?m=denied');
    exit();
}

if(!isset($_POST['T2']) && empty($_POST['T2'])) { 
    header('location:login.php?m=denied');
    exit();
}

require("config.php");
session_start();

$id = $_POST['T1'];
$tgl = $_POST['T2'];

if(!isset($_POST['T1']) && empty($_POST['T1'])) { 
    header('location:login.php?m=denied');
    exit();
} 


if(strlen(trim($id))<9){
    header('location:login.php?m=wrong_id');
    exit();
}


if(startsWith($id,"100")==false){ $id = '100'.$id; }
$id = str_replace('.', '', $id);


    


$result = $koneksi -> query("select * from pasien_pribadi where id = '$id' and tgl_lahir_enc = '$tgl' LIMIT 1");
if($row = $result -> fetch_array()){		
	$_SESSION['id'] = $id; 
	$_SESSION['nama_pasien'] = $row['nama']; 
	$_SESSION['no_hp'] = $row['verified_hp'];
	header('location:pilih_poli.php');
	exit();
	
}else{

    ///jika gagal cek barangkali PASIEN DAFTAR BARU VIA ONLINE
    $result = $koneksi -> query("select * from antrian_online_format_inc, pasien_baru where cib = id and tgl_lahir = '".toYYYY_MM_DD($tgl)."' LIMIT 1");
	if($row = $result -> fetch_array()){			
		if($row['id']==$id){
		    $_SESSION['id'] = $row['id']; 
		    $_SESSION['nama_pasien'] = $row['nama'];
		    $_SESSION['poli'] = $row['poli'];
		    $_SESSION['dokter'] = $row['dokter'];
		    $_SESSION['index_hari'] = $row['index_hari'];
		    $_SESSION['tanggal'] = invertDate($row['tgl']);
		    $_SESSION['jam'] = $row['jam'];
		    $_SESSION['no_online'] = $row['no_online'];
		    $_SESSION['kode_booking'] = $row['kode_booking'];
		    header('location:cetak.php');
		    exit();
	   	}else{
	       	    header('location:login.php?m=denied');
                    exit();
	   	}

	}else{
		header('location:login.php?m=denied');
                exit();
	}
}


function startsWith ($string, $startString){ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
} 


function toYYYY_MM_DD($txt){ 
      return substr($txt, 4, 4).'-'.substr($txt, 2, 2).'-'.substr($txt, 0, 2); 
} 


?>
