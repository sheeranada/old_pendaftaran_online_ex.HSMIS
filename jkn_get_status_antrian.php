<?php

include("tools.php");
include("jkn_get_jadwal_dokter_from_HFIS.php");

require("config.php");
date_default_timezone_set('Asia/Jakarta');


//////////////////////////////// HEADER TOKEN DO  NOT REMOVE //////////////////////////////////
$token = 'NOT_FOUND';
$token_valid = false;
foreach (getallheaders() as $name => $value) {
    if(strtolower($name)=='x-token'){ $token = $value; }
}




$query = $koneksi -> query("select * from token_active where token = '$token' and now() <= expired LIMIT 1");
while($row = $query -> fetch_array()){								
   $token_valid = true;
}

if($token_valid==false){
    echo '{"metadata":{"message":"Token Expired","code":201}}';
    exit();
}
//////////////////////////////// HEADER TOKEN DO  NOT REMOVE //////////////////////////////////


$jenisreferensi = 0;
$jenisrequest = 0;
$data = json_decode(file_get_contents('php://input'), true);
if(!empty($data)){
	$kode_poli = $data["kodepoli"];
        $kode_dokter = $data["kodedokter"];
	$tgl =  $data["tanggalperiksa"];
	$jampraktek =  $data["jampraktek"];
}else{
    echo '{"metadata":{"message":"INVALID DATA","code":201}}';
    exit();
}



$nama_dokter = '';
$query = $koneksi -> query("select * from dokter where kode_mapping_dpjp = '$kode_dokter' LIMIT 1");
while($row = $query -> fetch_array()){ $nama_dokter = $row['nama'];  }


//////////////////// KODE POLI SALAH ///////////////////////
$isFound = false;
$query = $koneksi -> query("select * from poli where kode_poli = '$kode_poli'  LIMIT 1");
while($row = $query -> fetch_array()){	$isFound = true; }
if($isFound==false){
    echo '{"metadata":{"message":"Poli Tidak Ditemukan","code":201}}';
    exit();
}

///////////////// CEK FORMAT TANGGAL INVALID
if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$tgl)==false) {
	echo '{"metadata":{"message":"Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd","code":201}}';
    exit();
}


$current = strtotime(date("Y-m-d"));
$date    = strtotime($tgl);
$datediff = $date - $current;
if($datediff<0){
   	echo '{"metadata":{"message":"Tanggal Periksa Tidak Berlaku","code":201}}';
    exit();
}



//$jampraktekHFIS = get_jam_praktek_dokter_by_HFIS($kode_poli, $tgl, $kode_dokter);
//if($jampraktek!=$jampraktekHFIS){
//    echo '{"metadata":{"message":"Jadwal Dokter '.$nama_dokter.' Tersebut Belum Tersedia, Silahkan Reschedule Tanggal dan Jam Praktek Lainnya","code":201}}';
//    exit();
//}

$total_antrean = 0;
$kuotanonjkn = 30;


$kuotajkn = 30;
//$kuotajkn = get_kuota_dokter_by_HFIS($kode_poli, $tgl, $kode_dokter);
$kuotanonjkn = $kuotajkn;

//if($kuotajkn==0){
//    echo '{"metadata":{"message":"Pendaftaran ke Poli Ini Sedang Tutup","code":201}}';
//    exit();
//}



///////HITUNG SISA KUOTA JKN //////////////////
$sisakuotajkn = 0;
$query = $koneksi -> query("select count(*) as cnt from antrian_online_format_inc where tgl = '$tgl' and dokter = '$nama_dokter' LIMIT 1");
while($row = $query -> fetch_array()){	$sisakuotajkn = $kuotajkn - $row['cnt'];   $total_antrean = $total_antrean + $row['cnt']; }


///////HITUNG SISA KUOTA NON JKN //////////////////
$sisakuotanonjkn =0;
$query = $koneksi -> query("select count(*) as cnt from antrian_online_format_inc where tgl = '$tgl' and dokter = '$nama_dokter' and length(no_kartu) = 0 LIMIT 1");
while($row = $query -> fetch_array()){	$sisakuotanonjkn = $kuotanonjkn - $row['cnt'];  $total_antrean = $total_antrean + $row['cnt']; }
//$total_antrean = $total_antrean;



$nama_poli = '';
$query = $koneksi -> query("select * from poli where kode_poli = '$kode_poli'  LIMIT 1");
while($row = $query -> fetch_array()){ 
 	$nama_poli = $row['nama_poli_ver_bpjs'];
}


$sudah_dilayani = 0;
$query = $koneksi -> query("select count(*) as cnt from antrian_online_format_inc where isSudahDilayani <> '' and tgl = '$tgl' and dokter = '$nama_dokter' and length(no_kartu) = 0 LIMIT 1");
while($row = $query -> fetch_array()){	$sudah_dilayani = $row['cnt'];  }


$sisa_antrean = $total_antrean -  $sudah_dilayani;
$antrean_dipanggil = $sudah_dilayani;

$myObj = new \stdClass();
$myObj->namapoli = $nama_poli;
$myObj->namadokter = $nama_dokter;
$myObj->totalantrean = $total_antrean;
$myObj->sisaantrean = $sisa_antrean;
$myObj->antreanpanggil = $antrean_dipanggil;
$myObj->sisakuotajkn = $sisakuotajkn;
$myObj->kuotajkn = $kuotajkn;

$myObj->sisakuotanonjkn = $sisakuotajkn;
$myObj->kuotanonjkn = $kuotajkn;

//$myObj->sisakuotanonjkn = $sisakuotanonjkn;
//$myObj->kuotanonjkn = $kuotanonjkn;


$myObj->keterangan = "";
$myJSON = json_encode($myObj);


$myRep = new \stdClass();
$myRep->message = "OK";
$myRep->code = 200;
$myRep = json_encode($myRep);

echo '{"response":',$myJSON.',"metadata":'.$myRep.'}';





?>