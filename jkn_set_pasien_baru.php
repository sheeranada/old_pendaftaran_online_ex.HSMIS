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

	$no_kartu = "";
    $nik = "";
    $no_kk = "";
    $nama = "";
    $sex = "";
	$tgl_lahir = "";
    $nohp = "";
    $alamat = "";
    $kodeprop = "";	
    $namaprop = "";
    $kodedati2 = "";
	$namadati2 = "";
	$kodekec ="";
	$namakec = "";
	$kodekel = "";
	$namakel ="";
	$rw = "";
	$rt = "";


$data = json_decode(file_get_contents('php://input'), true);
if(!empty($data)){
    $no_kartu = $data["nomorkartu"];
    $nik = $data["nik"];
    $no_kk = $data["nomorkk"];
    $nama = $data["nama"];
    $sex = $data["jeniskelamin"];
	$tgl_lahir =  $data["tanggallahir"];
    $nohp = $data["nohp"];
    $alamat = $data["alamat"];
    $kodeprop = $data["kodeprop"];	
    $namaprop = $data["namaprop"];
    $kodedati2 = $data["kodedati2"];
	$namadati2 = $data["namadati2"];
	$kodekec = $data["kodekec"];
	$namakec = $data["namakec"];
	$kodekel = $data["kodekel"];
	$namakel = $data["namakel"];
	$rw = $data["rw"];
	$rt = $data["rt"];
}else{
    echo '{"metadata":{"message":"INVALID DATA","code":201}}';
    exit();
}


///// no kartu harus 13 digit
if(strlen($no_kartu)==0){
    echo '{"metadata":{"message":"NO KARTU BELUM DIISI","code":201}}';
    exit();
}else if(strlen($no_kartu)!=13){
    echo '{"metadata":{"message":"NO KARTU BPJS HARUS 13 DIGIT","code":201}}';
    exit();
}else if(is_numeric($no_kartu)==false) {
     echo '{"metadata":{"message":"NO KARTU BPJS HARUS FORMAT NUMERIK","code":201}}';
    exit();
} 


///// no KTP NIK harus 16 digit
if(strlen($nik)!=16){
    echo '{"metadata":{"message":"NO NIK HARUS 16 DIGIT","code":201}}';
    exit();
}else if(is_numeric($nik)==false) {
    echo '{"metadata":{"message":"NO NIK HARUS FORMAT NUMERIK","code":201}}';
    exit();
} 


if(strlen($nama)==0){ echo '{"metadata":{"message":"NAMA BELUM DIISI","code":201}}';  exit(); }
if(strlen($alamat)==0){ echo '{"metadata":{"message":"ALAMAT BELUM DIISI","code":201}}';  exit(); }
if(strlen($no_kk)==0){ echo '{"metadata":{"message":"NO KK BELUM DIISI","code":201}}';  exit(); }
if(strlen($sex)==0){ echo '{"metadata":{"message":"JENIS KELAMIN BELUM DIISI","code":201}}';  exit(); }
if(strlen($tgl_lahir)==0){ echo '{"metadata":{"message":"TGL LAHIR BELUM DIISI","code":201}}';  exit(); }
if(strlen($kodeprop)==0){ echo '{"metadata":{"message":"KODE PROP BELUM DIISI","code":201}}';  exit(); }
if(strlen($namaprop)==0){ echo '{"metadata":{"message":"NAMA PROP BELUM DIISI","code":201}}';  exit(); }
if(strlen($kodedati2)==0){ echo '{"metadata":{"message":"KODE DATI 2 BELUM DIISI","code":201}}';  exit(); }
if(strlen($namadati2)==0){ echo '{"metadata":{"message":"NAMA DATI 2 BELUM DIISI","code":201}}';  exit(); }
if(strlen($kodekec)==0){ echo '{"metadata":{"message":"KODE KEC BELUM DIISI","code":201}}';  exit(); }
if(strlen($namakec)==0){ echo '{"metadata":{"message":"NAMA KEC BELUM DIISI","code":201}}';  exit(); }
if(strlen($kodekel)==0){ echo '{"metadata":{"message":"KODE KEL BELUM DIISI","code":201}}';  exit(); }
if(strlen($namakel)==0){ echo '{"metadata":{"message":"NAMA KEL BELUM DIISI","code":201}}';  exit(); }
if(strlen($rw)==0){ echo '{"metadata":{"message":"RW BELUM DIISI","code":201}}';  exit(); }
if(strlen($rt)==0){ echo '{"metadata":{"message":"RT BELUM DIISI","code":201}}';  exit(); }


if($sex=="L"){ $sex = 'Laki-laki';}
else{ $sex = 'Perempuan'; }

///////////////// CEK FORMAT TANGGAL INVALID
if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$tgl_lahir)==false) {
	echo '{"metadata":{"message":"Format Tanggal Lahir Tidak Sesuai","code":201}}';
    exit();
}


$no_rm = "";
$query = $koneksi -> query("select * from pasien_pribadi where no_kartu_bpjs = '$no_kartu'  LIMIT 1");
while($row = $query -> fetch_array()){  $$no_rm = $row['id']; }
if(strlen(trim($no_rm))>0){
    echo '{"metadata":{"message":"NO KARTU BPJS SUDAH TERDAFTAR DI RS DENGAN NO RM '.$no_rm.' SILAHKAN MENDAFTAR LANGSUNG UNTUK MENDAPATKAN ANTRIAN","code":201}}';
    exit();
}


$query = $koneksi -> query("select * from pasien_baru where no_bpjs = '$no_kartu' or no_ktp = '$nik' LIMIT 1");
while($row = $query -> fetch_array()){ 
    echo '{"metadata":{"message":"NO KARTU BPJS ATAU NIK SUDAH PERNAH MENDAFTAR DENGAN NO RM '.$row['id'].' '.$row['nama'].' ('.$row['tgl_lahir'].'), PENDAFTARAN PASIEN BARU HANYA BERLAKU 1X","code":201}}';
    exit();
}

$id = 1; 
$query = $koneksi -> query("select * from pasien_baru where id not like '100%' ORDER BY id DESC LIMIT 1");
while($row = $query -> fetch_array()){ $id = $row['id'] + 1; }

$query = mysqli_query($koneksi, "INSERT INTO pasien_baru values ('$id', '$nama', '$alamat', '$namakel', '$namakec', '$tgl_lahir', '$sex', '$nik', '$no_kartu', '$nohp', '$namadati2', '' )");

echo '{"response": {"norm": "'.$id.'" },"metadata": {"message": "Harap datang ke admisi untuk melengkapi data rekam medis","code": 200}}'


?>