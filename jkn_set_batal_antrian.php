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
    echo '{"metadata":{"message":"Token Expired","code":201}}';
    exit();
}
//////////////////////////////// HEADER TOKEN DO  NOT REMOVE //////////////////////////////////
   

$kodebooking = '';
$keterangan = '';
$data = json_decode(file_get_contents('php://input'), true);
if(!empty($data)){
    $kodebooking = $data["kodebooking"];
    $keterangan  = $data["keterangan"];
}else{
    echo '{"metadata":{"message":"Invalid Data","code":201}}';
    exit();
}

if(strlen($kodebooking)==0){ echo '{"metadata":{"message":"KODE BOOKING TIDAK BOLEH KOSONG","code":201}}';  exit(); }
if(strlen($keterangan)==0){ echo '{"metadata":{"message":"KETERANGAN TIDAK BOLEH KOSONG","code":201}}'; exit(); }

//////////////////// KODE POLI SALAH ///////////////////////
$isFound = false;
$query = $koneksi -> query("select * from antrian_online_format_inc where kode_booking = '$kodebooking'  LIMIT 1");
if($row = $query -> fetch_array()){
	if(strlen(trim($row['isSudahDilayani']))>0){
   	 	echo '{"metadata":{"message":"Pasien Sudah Dilayani, Antrean Tidak Dapat Dibatalkan","code": 201}}'; exit();
    }
	$isFound = true;
}


if($isFound==false){
    echo '{"metadata":{"message":"Antrean Tidak Ditemukan","code": 201}}';
    exit();
}


$koneksi -> query("DELETE from antrian_online_format_inc where kode_booking = '$kodebooking' ");
echo '{ "metadata": { "message": "Ok", "code": 200 } }';


?>