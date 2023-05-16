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
    echo '{"metadata":{"message":"TOKEN INVALID ATAU EXPIRED","code":"600"}}';
    exit();
}
//////////////////////////////// HEADER TOKEN DO  NOT REMOVE //////////////////////////////////


$jenisreferensi = 0;
$jenisrequest = 0;
$data = json_decode(file_get_contents('php://input'), true);
if(!empty($data)){
    $no_kartu = $data["nomorkartu"];
    $tgl =  $data["tanggalperiksa"];
    $kode_poli = $data["kodepoli"];
    $no_rujukan = $data["nomorreferensi"];
    $jenisreferensi = $data["jenisreferensi"];
    $jenisrequest = $data["jenisrequest"]; 
}else{
    echo '{"metadata":{"message":"INVALID DATA","code":"500"}}';
    exit();
}

if($jenisreferensi!=1&&$jenisreferensi!=2){
    echo '{"metadata":{"message":"KODE JENIS REFERENSI TIDAK SESUAI","code":"500"}}';
    exit();
}


if($jenisrequest!=1&&$jenisrequest!=2){
    echo '{"metadata":{"message":"KODE JENIS REQUEST TIDAK SESUAI","code":"500"}}';
    exit();
}

if(strlen($no_kartu)==0){
    echo '{"metadata":{"message":"NO KARTU TIDAK BOLEH KOSONG","code":"501"}}';
    exit();
}


//echo "select * from antrian_tpp where tgl = '$tgl' and no_kartu_bpjs = '$no_kartu' ORDER BY no DESC LIMIT 1";
//exit();

$query = $koneksi -> query("select * from antrian_tpp where tgl = '$tgl' and no_kartu_bpjs = '$no_kartu' ORDER BY no DESC LIMIT 1");
while($row = $query -> fetch_array()){								
    echo '{"metadata":{"message":"PASIEN SUDAH MENDAFTAR DI TANGGAL TERSEBUT","code":"502"}}';
    exit();
}


//////////////////// KODE POLI SALAH ///////////////////////
$isFound = false;
$query = $koneksi -> query("select * from poli where kode_poli = '$kode_poli'  LIMIT 1");
while($row = $query -> fetch_array()){	$isFound = true; }
if($isFound==false){
    echo '{"metadata":{"message":"KODE POLI('.$kode_poli.') TIDAK TERSEDIA Di REFERENSI","code":"501"}}';
    exit();
}



//////////////////// BACA HARI PRAKTEK DOKTERNYA ///////////////////////
$dayofweek = date('w', strtotime($tgl));
$isFound = false;
$query = $koneksi -> query("select * from tdok_jadwal, dokter, poli where index_hari = '$dayofweek' and dokter.nama = tdok_jadwal.nama and poli.poli = dokter.poli and poli.kode_poli = '$kode_poli'  LIMIT 1");
while($row = $query -> fetch_array()){	$isFound = true; }
if($isFound==false){
    //echo '{"metadata":{"message":"POLI TUTUP PADA HARI (DAY:'.$dayofweek.') TERSEBUT","code":"501"}}';
    echo '{"metadata":{"message":"POLI TUTUP PADA HARI TERSEBUT","code":"501"}}';
    exit();
}



$data = get_data_rujukan($no_rujukan);
///echo $data;
$data_rujukan = json_decode($data, true);
if(!empty($data_rujukan)){
    $metadata =  $data_rujukan["metaData"];
    $code = $metadata["code"];
    if($code!=200){
            echo '{"metadata":{"message":"DATA RUJUKAN '.$no_rujukan.' TIDAK DITEMUKAN","code":"502"}}';
            exit();
        
    }
    $response = $data_rujukan['response'];
    $rujukan = $response['rujukan'];
    $tglKunjungan = $rujukan['tglKunjungan'];
    $date90hari= date('Y-m-d H:i:s', strtotime($tglKunjungan . ' +90 day'));
 
     if(strtotime($tgl) > strtotime($date90hari)) {
        //echo '{"metadata":{"message":"TGL KUNJUNGAN '.$tgl.' TIDAK BOLEH LEBIH 90 HARI('.$date90hari.')","code":"502"}}';
        echo '{"metadata":{"message":"Tanggal Periksa sudah melebihi 90 hari dari tanggal rujukan FKTP","code":"502"}}';
        exit();
     }
}else{
    echo '{"metadata":{"message":"DATA RUJUKAN '.$no_rujukan.' TIDAK DITEMUKAN","code":"502"}}';
    exit();
}





$now = date("Y-m-d 00:00:00");
if(strtotime($tgl) <= strtotime($now)){
    echo '{"metadata":{"message":"TANGGAL DAFTAR BERLAKU UNTUK BESOK ATAU LUSA","code":"503"}}';
    exit();
 }
 
$now = date("Y-m-d h:m:s");
if(strtotime($tgl) == strtotime($now)){
    echo '{"metadata":{"message":"TANGGAL DAFTAR BERLAKU UNTUK BESOK ATAU LUSA","code":"504"}}';
    exit();
}
 

$datediff = strtotime($tgl) - strtotime($now);
$selisih = round($datediff / (60 * 60 * 24));

if($selisih>3){
    echo '{"metadata":{"message":"TANGGAL DAFTAR MAKSIMAL H+2","code":"505"}}';
    exit();
 }
 

if(strlen($no_kartu)!=13){
    echo '{"metadata":{"message":"NO KARTU HARUS 13 DIGIT","code":"506"}}';
    exit();
}





$isFound = false;
$query = $koneksi -> query("select * from pasien_pribadi where no_kartu_bpjs = '$no_kartu'  LIMIT 1");
while($row = $query -> fetch_array()){								
   $isFound = true;
}


if($isFound==false){
    echo '{"metadata":{"message":"NO KARTU BPJS BELUM TERDAFTAR DI RS SILAHKAN MENDAFTAR LANGSUNG UNTUK MENDAPATKAN NO RM (BERLAKU 1X DI KUNJUNGAN PERTAMA)","code":"506"}}';
    exit();
}




$lama_tunggu = 3;              /// Default 3 menit
$no_online = 10;               /// Start nomor
$jam_buka = $tgl." 6:00:00";   /// Default Jam Buka


$nama_poli = $kode_poli;
$query = $koneksi -> query("select * from antrian_tpp where tgl = '$tgl' ORDER BY no DESC LIMIT 1");
while($row = $query -> fetch_array()){								
	$no_online = $row['no'] + 1;
}
$kode_booking = substr(rand(),-6);


$query = $koneksi -> query("select * from poli where  kode_poli = '$kode_poli'  LIMIT 1");
while($row = $query -> fetch_array()){								
   $lama_tunggu = $row['avg_service_time'];
   $nama_poli = $row['poli'];
    
}
$lama_tunggu = $lama_tunggu * $no_online;
$estimasi_dilayani = strtotime("+".$lama_tunggu." minutes", strtotime($jam_buka)) * 1000; ///dalam milisecond



$query = mysqli_query($koneksi, "INSERT INTO antrian_tpp values ('$no_online', '$tgl', '$no_kartu', '$kode_booking', '', '$kode_poli' )");
$myObj = new \stdClass();
$myObj->nomorantrean = "$no_online";
$myObj->kodebooking = $kode_booking;
$myObj->jenisantrean = 2;
$myObj->estimasidilayani = $estimasi_dilayani;
$myObj->namapoli = $nama_poli;
$myJSON = json_encode($myObj);


$myRep = new \stdClass();
$myRep->message = "OK";
$myRep->code = "200";
$myRep = json_encode($myRep);

echo '{"response":',$myJSON.',"metadata":'.$myRep.'}';





?>