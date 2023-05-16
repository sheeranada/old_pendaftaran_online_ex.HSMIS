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


$data = json_decode(file_get_contents('php://input'), true);
if(!empty($data)){
    $kodebooking = $data["kodebooking"];
    $waktu = $data["waktu"];
}else{
    echo '{"metadata":{"message":"INVALID DATA","code":201}}';
    exit();
}


if(strlen($kodebooking)==0){ echo '{"metadata":{"message":"KODE BOOKING TIDAK BOLEH KOSONG","code":201}}'; exit(); }
if(strlen($waktu)==0){ echo '{"metadata":{"message":"WAKTU TIDAK BOLEH KOSONG","code":201}}'; exit(); }

$isFound = false;
$query = $koneksi -> query("select * from antrian_online_format_inc where kode_booking = '$kodebooking'  LIMIT 1");
while($row = $query -> fetch_array()){ $isFound = true; }
if($isFound==false){  echo '{"metadata":{"message":"KODE BOOKING('.$kodebooking.') TIDAK DITEMUKAN ATAU SUDAH DIBATALKAN","code":201}}'; exit(); }


$query = mysqli_query($koneksi, "UPDATE antrian_online_format_inc set isSudahDilayani = 'CHECK IN' where kode_booking = '$kodebooking'");

$query = mysqli_query($koneksi, "INSERT INTO check_in values ('$kodebooking', now(), '', '', '', '', '', '', '', '', '', '' )");
echo '{ "metadata": { "message": "Ok", "code": 200 } }';

?>