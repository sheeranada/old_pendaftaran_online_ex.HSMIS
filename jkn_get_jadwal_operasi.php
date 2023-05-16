<?php

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


$tanggalawal = "";
$tanggalakhir = "";

$json = file_get_contents('php://input');
$data = json_decode($json, true);
if(!empty($data)){
    $tanggalawal = $data["tanggalawal"];
    $tanggalakhir =  $data["tanggalakhir"];
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
$query = mysqli_query($koneksi, "INSERT INTO xsql_log values (now(), 'jkn_get_jadwal_operasi.php', '$json', '$ipaddress' )");



if(strtotime($tanggalawal) > strtotime($tanggalakhir)) {
    echo '{"metadata":{"message":"TGL AWAL '.$tanggalawal.' TIDAK BOLEH LEBIH BESAR '.$tanggalakhir.'","code":201}}';
    exit();
}






$tm_stamp = round(microtime(true) * 1000); ///dalam microsecond

$data_log = '';
$query = $koneksi -> query("select * from jadwal_operasi where tgl_operasi >= '$tanggalawal' and tgl_operasi <= '$tanggalakhir' ");
while($row = $query -> fetch_array()){				
   $isSudah = $row['isSudah'];
   if(strlen($isSudah)==0){ $isSudah = '0'; }

   $data_log = $data_log.'{';
   $data_log = $data_log.'"kodebooking": "'.$row['no_reg'].'",';
   $data_log = $data_log.'"tanggaloperasi": "'.$row['tgl_operasi'].'",';
   $data_log = $data_log.'"jenistindakan": "'.$row['jenis_operasi'].'",';
   $data_log = $data_log.'"kodepoli": "'.$row['kode_poli_dpjp'].'",';
   $data_log = $data_log.'"namapoli": "'.$row['nama_poli_dpjp'].'",';
   $data_log = $data_log.'"terlaksana": '.$isSudah.',';
   $data_log = $data_log.'"nopeserta": "'.$row['no_kartu'].'",';
   $data_log = $data_log.'"lastupdate": '.$tm_stamp.' ';
   $data_log = $data_log.'},';
}


        
        
echo '{"response": { "list" : ['; 
echo substr($data_log, 0, -1);        
echo '] }, "metadata": { "message": "Ok", "code": 200 } }';

?>