<?
include 'decompress.php';


//echo "200<br>";
//echo get_kuota_dokter_by_HFIS('INT','2022-06-21','310178');



function cek_jadwal($kode_poli, $tgl, $kodedokter){
   
//$URL = 'https://apijkn-dev.bpjs-kesehatan.go.id/antreanrs_dev';   //DEVELOPMENT:
 

////REKSO WALUYO/////
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
    return 0;
}

 $key = $cons_id.$secretKey.$tStamp;
 $decrypt_data = stringDecrypt($key, $response);
 $jadwal = decompress($decrypt_data);

 $isFound = false;
 $jadwal = json_decode($jadwal, true);
 if(!empty($jadwal)){
    for($i=0;$i<=count($jadwal)-1;$i++){
        if($jadwal[$i]["kodedokter"]==$kodedokter){ $isFound = true; }
    }
 }else{
     echo '{"metadata":{"message":"Pendaftaran ke Poli Ini Sedang Tutup","code":201}}';
     return 0;
 }


 if($isFound==false){
     echo '{"metadata":{"message":"Jadwal Dokter '.$kodedokter.' Tersebut Belum Tersedia, Silahkan Reschedule Tanggal dan Jam Praktek Lainnya","code":201}}';
     return 0;
 }

 return 1;
}




function get_kuota_dokter_by_HFIS($kode_poli, $tgl, $kodedokter){
   
////REKSO WALUYO/////
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


function get_jam_praktek_dokter_by_HFIS($kode_poli, $tgl, $kodedokter){
   

////REKSO WALUYO/////
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
            return $jadwal[$i]["jadwal"];
        }
    }
}

return 0;
    
}

?>