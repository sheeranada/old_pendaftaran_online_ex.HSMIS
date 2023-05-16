<?php
include("tools.php");
require("config.php");
include("decompress.php");

date_default_timezone_set('Asia/Jakarta');


//////////////////////////////// HEADER TOKEN DO  NOT REMOVE //////////////////////////////////
$token = 'NOT_FOUND';
$token_valid = false;
foreach (getallheaders() as $name => $value) {
    if(strtolower($name)=='x-token'){ $token = $value; }
}


$query = $koneksi -> query("select * from token_active where token = '$token' and now() <= expired LIMIT 1");
if($row = $query -> fetch_array()){ $token_valid = true; }
if($token_valid==false){
	echo '{"metadata":{"message":"Token Expired","code":201}}';
    exit();
}
//////////////////////////////// HEADER TOKEN DO  NOT REMOVE //////////////////////////////////
   

$nik = '';
$nohp = '';
$norm = '';
$kodedokter = '';
$jampraktek = '';
$no_rujukan = '';
$jeniskunjungan= 0;
$kode_poli = '';
$tgl = '';


$json = file_get_contents('php://input');
$data = json_decode($json, true);
if(!empty($data)){
    $no_kartu = $data["nomorkartu"];
    $tgl =  $data["tanggalperiksa"];
    $kode_poli = $data["kodepoli"];
    $no_rujukan = $data["nomorreferensi"];
    $nik = $data["nik"];
	$norm = $data["norm"];
	$nohp = $data["nohp"];
	$kodedokter = $data["kodedokter"];
	$jampraktek = $data["jampraktek"];
	$jeniskunjungan	= $data["jeniskunjungan"];
}else{
    echo '{"metadata":{"message":"INVALID DATA","code":201}}';
    exit();
}


$ipaddress = '';
if (getenv('HTTP_CLIENT_IP'))
    $ipaddress = getenv('HTTP_CLIENT_IP');
else if(getenv('HTTP_X_FORWARDED_FOR'))
     $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
else if(getenv('HTTP_X_FORWARDED'))
     $ipaddress = getenv('HTTP_X_FORWARDED');
else if(getenv('HTTP_FORWARDED_FOR'))
     $ipaddress = getenv('HTTP_FORWARDED_FOR');
else if(getenv('HTTP_FORWARDED'))
     $ipaddress = getenv('HTTP_FORWARDED');
else if(getenv('REMOTE_ADDR'))
     $ipaddress = getenv('REMOTE_ADDR');
else
     $ipaddress = 'UNKNOWN';
$query = mysqli_query($koneksi, "INSERT INTO xsql_log values (now(), 'jkn_get_antrian.php', '$json', '$ipaddress' )");


$jamX = explode('-',$jampraktek);
$jamPraktek_DariJSON = $jamX[0]; 

if(strlen($no_kartu)!=13){ echo '{"metadata":{"message":"NO KARTU HARUS 13 DIGIT","code":201}}';  exit(); }

//if($jeniskunjungan!=1&&$jeniskunjungan!=2){ echo '{"metadata":{"message":"Jenis kunjungan tidak sesuai","code":201}}';  exit(); }


if(strlen($no_kartu)==0){ echo '{"metadata":{"message":"NO KARTU TIDAK BOLEH KOSONG","code":201}}'; exit(); }
if(strlen($nik)==0){ echo '{"metadata":{"message":"NIK TIDAK BOLEH KOSONG","code":201}}'; exit(); }
//if(strlen($norm)==0){ echo '{"metadata":{"message":"NO RM TIDAK BOLEH KOSONG","code":201}}'; exit(); }
if(strlen($nohp)==0){ echo '{"metadata":{"message":"NO HP TIDAK BOLEH KOSONG","code":201}}'; exit(); }
if(strlen($kode_poli)==0){ echo '{"metadata":{"message":"KODE POLI TIDAK BOLEH KOSONG","code":201}}'; exit(); }
if(strlen($kodedokter)==0){ echo '{"metadata":{"message":"KODE DOKTER TIDAK BOLEH KOSONG","code":201}}'; exit(); }
if(strlen($jampraktek)==0){ echo '{"metadata":{"message":"JAM PRAKTEK TIDAK BOLEH KOSONG","code":201}}'; exit(); }
if(strlen($jeniskunjungan)==0){ echo '{"metadata":{"message":"JENIS KUNJUNGAN TIDAK BOLEH KOSONG","code":201}}'; exit(); }


if(is_numeric($no_kartu)==false) { echo '{"metadata":{"message":"NO KARTU HARUS NUMERIK","code":201}}';  exit(); }
if(is_numeric($nik)==false) { echo '{"metadata":{"message":"NIK HARUS NUMERIK","code":201}}';  exit(); }
//if(is_numeric($norm)==false) { echo '{"metadata":{"message":"NO RM HARUS NUMERIK","code":201}}';  exit(); }
if(is_numeric($kodedokter)==false) { echo '{"metadata":{"message":"KODE DOKTER HARUS NUMERIK","code":201}}';  exit(); }


//////////////////// KODE POLI SALAH ///////////////////////
$namapoli = '';
$lama_tunggu = 10; //default 10 menit
$query = $koneksi -> query("select * from poli where kode_poli = '$kode_poli'  LIMIT 1");
while($row = $query -> fetch_array()){	$namapoli = $row["poli"]; $lama_tunggu = $row['avg_service_time']; }
if($namapoli==''){
    echo '{"metadata":{"message":"KODE POLI('.$kode_poli.') TIDAK TERSEDIA Di REFERENSI","code":201}}';
    exit();
}


//$query = $koneksi -> query("select * from pasien_baru where no_bpjs = '$no_kartu'  LIMIT 1");
//if($row = $query -> fetch_array()){ 
    //echo '{"metadata":{"message":"Pasien Baru silahkan menuju adminisi untuk melengkapi berkas","code":200}}';
//    echo '{ "response": { "norm": "'.$row['id'].'"   },   "metadata": {  "message": "Test pasien baru....", "code": 200 }}';
//    exit();
//}



$isFound = false;
$query = $koneksi -> query("select * from pasien_pribadi where no_kartu_bpjs = '$no_kartu'  LIMIT 1");
if($row = $query -> fetch_array()){ $norm = $row['id'];  $isFound = true; }
$query = $koneksi -> query("select * from pasien_baru where no_bpjs = '$no_kartu'  LIMIT 1");
if($row = $query -> fetch_array()){ $isFound = true; }
$query = $koneksi -> query("select * from pasien_baru where id = '$norm'  LIMIT 1");
if($row = $query -> fetch_array()){ $isFound = true; }
if($isFound==false){
    echo '{"metadata":{"message":"Data pasien ini tidak ditemukan, silahkan Melakukan Registrasi Pasien Baru","code":202}}';
    exit();
}


///////////////// CEK FORMAT TANGGAL INVALID
if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$tgl)==false) {
	echo '{"metadata":{"message":"Tanggal Periksa Invalid","code":201}}';
    exit();
}

$query = $koneksi -> query("select * from antrian_online_format_inc where no_kartu = '$no_kartu' and tgl = '$tgl' LIMIT 1");
if($row = $query -> fetch_array()){								
    echo '{"metadata":{"message":"Nomor Antrean Hanya Dapat Diambil 1 Kali Pada Tanggal Yang Sama","code":201}}';
    exit();
}





//////////////////// BACA HARI PRAKTEK DOKTERNYA ///////////////////////
$dayofweek = date('w', strtotime($tgl));
$jambuka = '2000-10-10 00:00:00';
$now = date("Y-m-d 00:00:00");
if(strtotime($tgl) <= strtotime($now)){
    echo '{"metadata":{"message":"Pendaftaran Ke Poli Backdate","code":201}}';
    exit();
}
 

//$now_jam_12 = date("Y-m-d 12:00:00");
//if(strtotime($tgl) <= strtotime($now_jam_12)){
//    echo '{"metadata":{"message":"Pendaftaran Ke Poli Sudah Tutup Jam  12.00","code":201}}';
//    exit();
//}


$now = date("Y-m-d h:m:s");
if(strtotime($tgl) == strtotime($now)){ 
	echo '{"metadata":{"message":"Tanggal Daftar berlaku untuk besok atau lusa","code":201}}';  
	exit(); 
}
 

$datediff = strtotime($tgl) - strtotime($now);
$selisih = round($datediff / (60 * 60 * 24));

//if($selisih>3){
//    echo '{"metadata":{"message":"TANGGAL DAFTAR MAKSIMAL H+2","code":201}}';
//    exit();
//}

if($selisih>90){
    echo '{"metadata":{"message":"TANGGAL DAFTAR MAKSIMAL H-90","code":201}}';
    exit();
}


$namadokter = $kodedokter;
$query = $koneksi -> query("select * from dokter where kode_mapping_dpjp = '$kodedokter' LIMIT 1");
while($row = $query -> fetch_array()){	$namadokter = $row['nama']; }
if($namadokter==$kodedokter){
    echo '{"metadata":{"message":"Kode Dokter '.$kodedokter.' belum dilakukan mapping","code":201}}';
    exit();
}


////CEK JADWAL HFIS
//$cek_jadwal = cek_dokter_apa_praktek($kode_poli, $tgl, $kodedokter, $jampraktek);
//if(strlen($cek_jadwal)==0){ exit(); }


$poli = $kode_poli;
$query = $koneksi -> query("select * from poli where kode_poli = '$kode_poli' LIMIT 1");
while($row = $query -> fetch_array()){ $poli = $row['poli']; }


$kuotajkn = 30;
//$kuotajkn = get_kuota_dokter_by_HFIS($kode_poli, $tgl, $kodedokter);
$kuotanonjkn = $kuotajkn;


$no_online = 1; ///Start nomor
$query = $koneksi -> query("select * from antrian_online_format_inc where no_online >= '$no_online' and dokter = '$namadokter' and tgl = '$tgl' ORDER BY no_online DESC LIMIT 1");
if($row = $query -> fetch_array()){	$no_online = $row['no_online'] + 1; }



$index_hari = 0;
$query = $koneksi -> query("select dayofweek('$tgl') as t");
if($row = $query -> fetch_array()){ $index_hari = $row['t'] - 1; }


$jam_buka = $jamPraktek_DariJSON.':00';
///$jam_buka = '08:00:00';
//$query = $koneksi -> query("select * from tdok_jadwal where index_hari = '$index_hari' and nama = '$namadokter' LIMIT 1");
//if($row = $query -> fetch_array()){ $jam_buka = $row['jam_mulai']; }

$tgl_jam_buka = $tgl." ".$jam_buka;   /// Default Jam Buka
$lama_tunggu = $lama_tunggu * ($no_online -1);


//$UTC = strtotime("+".$lama_tunggu." minutes", strtotime($tgl_jam_buka)) * 1000; ///dalam milisecond

$UTC = strtotime("+".$lama_tunggu." minutes", strtotime($tgl_jam_buka)); ///dalam detik
$estimasi_dilayani = strtotime("0 hours", $UTC) * 1000;


$sisakuotajkn = $kuotajkn;
$sisakuotanonjkn = $kuotanonjkn;
/////// A. BACA SISA KUOTA JKN /////
$query = $koneksi -> query("select count(*) as cnt from antrian_online_format_inc where length(no_kartu) > 0 LIMIT 1");
if($row = $query -> fetch_array()){	$sisakuotajkn = $sisakuotajkn - $row['cnt']; }
if($sisakuotajkn<0){ $sisakuotajkn = 0; }


/////// B. BACA SISA KUOTA NON JKN NON JKN /////
$query = $koneksi -> query("select count(*) as cnt from antrian_online_format_inc, pasien_pribadi where cib = id and tgl = '$tgl' and length(no_kartu_bpjs) = 0 LIMIT 1");
if($row = $query -> fetch_array()){	$sisakuotanonjkn = $sisakuotanonjkn - $row['cnt']; }
$query = $koneksi -> query("select count(*) as cnt from antrian_online_format_inc, pasien_baru where cib = id and tgl = '$tgl' and length(no_bpjs) = 0 LIMIT 1");
if($row = $query -> fetch_array()){	$sisakuotanonjkn = $sisakuotanonjkn - $row['cnt']; }
if($sisakuotanonjkn<0){ $sisakuotanonjkn = 0; }


///$kode_booking = substr(rand(),-8);
$kode_booking = str_replace("-","",$tgl).rand(10,100);



$query = mysqli_query($koneksi, "INSERT INTO antrian_online_format_inc values ('$namadokter', '$tgl', '$jam_buka', '$no_online', '$norm', '$index_hari', '$poli' ,
'$kode_booking', '$no_kartu', '$nik', '', now(), '', '' )");

$isBerhasilMasuk = false;
$query = $koneksi -> query("select * from antrian_online_format_inc where kode_booking = '$kode_booking' LIMIT 1");
if($row = $query -> fetch_array()){ $isBerhasilMasuk = true; }

if($isBerhasilMasuk==true){
	$myObj = new \stdClass();
	$myObj->nomorantrean = "$no_online";
	$myObj->angkaantrean = $no_online;
	$myObj->kodebooking = "$kode_booking";
	$myObj->norm = "$norm";
	$myObj->namapoli = "$namapoli";
	$myObj->namadokter = "$namadokter";
	$myObj->estimasidilayani = $estimasi_dilayani;
        $myObj->sisakuotajkn = $sisakuotajkn;	
        $myObj->kuotajkn = $kuotajkn;
	$myObj->sisakuotanonjkn = $sisakuotanonjkn;
	$myObj->kuotanonjkn = $kuotanonjkn;
	$myObj->namapoli = $namapoli;
	$myObj->keterangan = "Peserta harap hadir 60 menit lebih awal guna pencatatan administrasi.";
	$myJSON = json_encode($myObj);

	$myRep = new \stdClass();
	$myRep->message = "OK";
	$myRep->code = 200;
	$myRep = json_encode($myRep);
	echo '{"response":',$myJSON.',"metadata":'.$myRep.'}';
	exit();
	
}else{
	echo '{"metadata":{"message":"Ada kesalahan saat menyimpan data","code":201}}';
 	exit();
}




///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

function get_kuota_dokter_by_HFIS($kode_poli, $tgl, $kodedokter){
   
///REKSA WALUYO
$URL = 'https://apijkn.bpjs-kesehatan.go.id/antreanrs';
$cons_id = "14540";
$secretKey = "7iX21C7226";
$user_key = "60706a8b21afdcc6090792bf3fdb3b6a";
	
date_default_timezone_set('UTC');
$tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
$signature = hash_hmac('sha256', $cons_id."&".$tStamp, $secretKey, true);
$encodedSignature = base64_encode($signature);
$headers = [
    'X-cons-id: '.$cons_id,
    'X-timestamp:' .$tStamp,
    'X-signature: '.$encodedSignature,
    'user_key: '.$user_key
];

$URL = $URL.'/jadwaldokter/kodepoli/'.$kode_poli.'/tanggal/'.$tgl;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$server_output = curl_exec($ch);
curl_close ($ch);

$data = json_decode($server_output, true);
if(!empty($data)){
    $response = $data["response"];
}else{
    return 0;
}

$key = $cons_id.$secretKey.$tStamp;
$decrypt_data = stringDecrypt($key, $response);
$jadwal = decompress($decrypt_data);
$jadwal = json_decode($jadwal, true);
if(!empty($jadwal)){
    for($i=0;$i<=count($jadwal)-1;$i++){
        if($jadwal[$i]["kodedokter"]==$kodedokter){ 
            return $jadwal[$i]["kapasitaspasien"];
        }
    }
}
return 0;
}
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////









///////////////////////////////////////////////////////////////////////////////
///////////// FUNGSI UNTUK CEK JADWAL DOKTERNYA ///////////////////////////////
//////////////////////////////////////////////////////////////////////////////
function cek_dokter_apa_praktek($kode_poli, $tgl, $kodedokter, $jampraktek){


///REKSA WALUYO
$URL = 'https://apijkn.bpjs-kesehatan.go.id/antreanrs';
$cons_id = "14540";
$secretKey = "7iX21C7226";
$user_key = "60706a8b21afdcc6090792bf3fdb3b6a";


date_default_timezone_set('UTC');
$tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
$signature = hash_hmac('sha256', $cons_id."&".$tStamp, $secretKey, true);
$encodedSignature = base64_encode($signature);
$headers = [
    'X-cons-id: '.$cons_id,
    'X-timestamp:' .$tStamp,
    'X-signature: '.$encodedSignature,
    'user_key: '.$user_key
];


$URL = $URL.'/jadwaldokter/kodepoli/'.$kode_poli.'/tanggal/'.$tgl;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$server_output = curl_exec($ch);
curl_close ($ch);

$data = json_decode($server_output, true);	
if(!empty($data)){
    $response = $data["response"];
}else{
    echo '{"metadata":{"message":"Pendaftaran ke Poli Ini Sedang Tutup","code":201}}';
    return '';
}

	
$key = $cons_id.$secretKey.$tStamp;
$decrypt_data = stringDecrypt($key, $response);
$jadwal = decompress($decrypt_data);
$jadwal = json_decode($jadwal, true);
	
$namaDokter = '';	
if(!empty($jadwal)){
    for($i=0;$i<=count($jadwal)-1;$i++){
	if($jadwal[$i]["kodedokter"]==$kodedokter){ 
	       	if($jadwal[$i]["jadwal"]!=$jampraktek){ 
		     echo '{"metadata":{"message":"Pendaftaran ke Poli Ini Sedang Tutup","code":201}}';
	     		return ''; 
		}	
		$namaDokter = $jadwal[$i]["namadokter"]; 
	}
    }
 }else{
     echo '{"metadata":{"message":"Pendaftaran ke Poli Ini Sedang Tutup","code":201}}';
     return '';
 }


 if(strlen($namaDokter)==0){
     echo '{"metadata":{"message":"Jadwal Dokter Belum Tersedia, Silahkan Reschedule Tanggal dan Jam Praktek Lainnya","code":201}}';
     return '';
 }
 return $namaDokter;
}


?>