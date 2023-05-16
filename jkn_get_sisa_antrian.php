<?php
include("tools.php");
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
    echo '{"metadata":{"message":"TOKEN INVALID ATAU EXPIRED","code":201}}';
    exit();
}
//////////////////////////////// HEADER TOKEN DO  NOT REMOVE //////////////////////////////////


$kodebooking = '';
$data = json_decode(file_get_contents('php://input'), true);
if(!empty($data)){
    $kodebooking = $data["kodebooking"];
}else{
    echo '{"metadata":{"message":"INVALID DATA","code":201}}';
    exit();
}
if(strlen($kodebooking)==0){ echo '{"metadata":{"message":"KODE BOOKING TIDAK BOLEH KOSONG","code":201}}';  exit(); }

$kodepoli = '';
$namapoli = '';
$namadokter = '';
$sisa_antrian = '';
$antrian_panggil = '0';
$waktutunggu = '';
$isFound = false;



$no_online = '0';
$query = $koneksi -> query("select * from antrian_online_format_inc where kode_booking = '$kodebooking'  LIMIT 1");
while($row = $query -> fetch_array()){
	$isFound = true;
	$no_online = $row['no_online'];
	$namapoli = $row['poli'];
	$namadokter = $row['dokter'];
}

if($isFound==false){
    echo '{"metadata":{"message":"KODE BOOKING ('.$kodebooking.') TIDAK DITEMUKAN ATAU SUDAH DIBATALKAN","code":201}}';
    exit();
}

$avg_waktu = 10;
$query = $koneksi -> query("select * from poli where poli = '$namapoli'  LIMIT 1");
while($row = $query -> fetch_array()){ $namapoli = $row['poli'];  $avg_waktu = $row['avg_service_time']; }


//echo "select count(*) as cnt from antrian_online_format_inc where isSudahDilayani = 'CHECK IN' and poli = '$namapoli' and tgl = curdate()";
$query = $koneksi -> query("select count(*) as cnt from antrian_online_format_inc where isSudahDilayani = 'CHECK IN' and poli = '$namapoli' and tgl = curdate()");
while($row = $query -> fetch_array()){	$antrian_panggil = $row['cnt']; }


$panggilx = 0;
$query = $koneksi -> query("select count(*) as cnt from antrian_online_format_inc where isSudahDilayani = 'PANGGIL' and poli = '$namapoli' and tgl = curdate()");
while($row = $query -> fetch_array()){	$panggilx = $row['cnt']; }

$sisa_antrian = $no_online - $antrian_panggil;
$waktutunggu = $sisa_antrian * $avg_waktu;

$keterangan = '';
$myObj = new \stdClass();
$myObj->nomorantrean = "$no_online";
$myObj->namapoli = $namapoli;
$myObj->namadokter = $namadokter;
$myObj->sisaantrean = $antrian_panggil;
$myObj->antreanpanggil = "$panggilx";
$myObj->waktutunggu = ($waktutunggu * 60);
$myObj->keterangan = $keterangan;
$myJSON = json_encode($myObj);

        
$myRep = new \stdClass();
$myRep->message = "OK";
$myRep->code = 200;
$myRep = json_encode($myRep);

echo '{"response":',$myJSON.',"metadata":'.$myRep.'}';


?>