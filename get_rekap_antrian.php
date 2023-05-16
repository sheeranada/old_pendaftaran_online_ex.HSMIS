<?php

require("config.php");

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


$data = json_decode(file_get_contents('php://input'), true);
$tgl_periksa = '2019-01-01';
$kodepoli = '001';

$tgl_periksa = '9999-01-01';
if(!empty($data)){
    $tgl_periksa = $data["tanggalperiksa"];
    $kodepoli =  $data["kodepoli"];
    $polieksekutif = $data["polieksekutif"];
}else{
    echo '{"metadata":{"message":"INVALID DATA","code":"500"}';
    exit();
}



if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$tgl_periksa)) {
   /*True Do nothing*/
} else {
    echo '{"metadata":{"message":"TANGGAL PERIKSA TIDAK VALID FORMAT YYYY-MM-DD","code":"501"}}';
    exit();
}


//echo 'xxxx';
//exit();

if(validateDate($tgl_periksa)==false){
    echo '{"metadata":{"message":"TANGGAL PERIKSA TIDAK VALID SESUAI KALENDER","code":"501"}}';
    exit();
}



$isfound = false;
$namapoli = 'XXXXX';
$query = $koneksi -> query("select * from poli where kode_poli = '$kodepoli' LIMIT 1");
while($row = $query -> fetch_array()){								
   $namapoli = $row['poli'];
   $isfound = true;
}
if($isfound==false){
    echo '{"metadata":{"message":"KODE POLI ('.$kodepoli.') TIDAK SESUAI","code":"502"}}';
    exit();
}



$total_antrian = 0;
$query = $koneksi -> query("select count(*) as tot from antrian_tpp where tgl = '$tgl_periksa' and kode_poli = '$kodepoli' LIMIT 1");
while($row = $query -> fetch_array()){								
   $total_antrian = $row['tot'];
}

$terlayani = 0;
$query = $koneksi -> query("select count(*) as tot from antrian_tpp where tgl = '$tgl_periksa' and kode_poli = '$kodepoli' and isSudahDilayani = 'YES' LIMIT 1");
while($row = $query -> fetch_array()){								
   $terlayani = $row['tot'];
}





$tm_stamp = time();
$tgl_format = date("Y-m-d H:i:s", $tm_stamp);

echo  '{"response": {';
echo  '"namapoli" : "'.$namapoli.'",';
echo  '"totalantrean" : '.$total_antrian.',';
echo  '"jumlahterlayani" : '.$terlayani.',';
echo  '"lastupdate" : '.$tm_stamp.',';
echo  '"lastupdatetanggal" : "'.$tgl_format.'"';
echo  '},"metadata": { "message": "Ok", "code": 200 } }';




function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

?>