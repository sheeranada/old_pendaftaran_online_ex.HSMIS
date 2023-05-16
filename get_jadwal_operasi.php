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
    echo '{"metadata":{"message":"TOKEN INVALID ATAU EXPIRED","code":"600"}}';
    exit();
}
//////////////////////////////// HEADER TOKEN DO  NOT REMOVE //////////////////////////////////


$tanggalawal = "";
$tanggalakhir = "";
//$kode_poli = "";

$data = json_decode(file_get_contents('php://input'), true);
if(!empty($data)){
    $tanggalawal = $data["tanggalawal"];
    $tanggalakhir =  $data["tanggalakhir"];
    //$kode_poli = $data["kodepoli"];
    //echo $data["nomorrm"];
    //echo $data["nomorreferensi"];
}else{
    echo '{"metadata":{"message":"INVALID DATA","code":"500"}}';
    exit();
}


if(strtotime($tanggalawal) > strtotime($tanggalakhir)) {
    echo '{"metadata":{"message":"TGL AWAL '.$tanggalawal.' TIDAK BOLEH LEBIH BESAR '.$tanggalakhir.'","code":"502"}}';
    exit();
}






$tm_stamp = round(microtime(true) * 1000); ///dalam microsecond

$data_log = '';
$query = $koneksi -> query("select * from jadwal_operasi where tgl_operasi >= '$tanggalawal' and tgl_operasi <= '$tanggalakhir' ");
while($row = $query -> fetch_array()){								
   $data_log = $data_log.'{';
   $data_log = $data_log.'"kodebooking": "'.$row['no_reg'].'",';
   $data_log = $data_log.'"tanggaloperasi": "'.$row['tgl_operasi'].'",';
   $data_log = $data_log.'"jenistindakan": "'.$row['jenis_operasi'].'",';
   $data_log = $data_log.'"kodepoli": "'.$row['kode_poli_dpjp'].'",';
   $data_log = $data_log.'"namapoli": "'.$row['nama_poli_dpjp'].'",';
   $data_log = $data_log.'"terlaksana": '.$row['isSudah'].',';
   $data_log = $data_log.'"nopeserta": "'.$row['no_kartu'].'",';
   $data_log = $data_log.'"lastupdate": '.$tm_stamp.' ';
   $data_log = $data_log.'},';
}


        
        
echo '{"response": { "list" : ['; 
echo substr($data_log, 0, -1);        
echo '] }, "metadata": { "message": "Ok", "code": 200 } }';

?>