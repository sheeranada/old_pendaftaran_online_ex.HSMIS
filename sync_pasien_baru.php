<?php 

require("config.php");
session_start();


$txt = "";
$query = $koneksi -> query("select * from pasien_baru, antrian_online_format_inc where cib = id and isDownload <> 'YES' and tgl >= curdate() ");
while($row = $query -> fetch_array()){
	
	echo $row['id'].
		'@'.$row['nama'].
		'@'.$row['alamat'].
		'@'.$row['kelurahan'].
		'@'.$row['kecamatan'].
		'@'.$row['tgl_lahir'].
		'@'.$row['sex'].
		'@'.$row['no_ktp'].
		'@'.$row['no_bpjs'].
		'@'.$row['kota'].
		'@'.$row['telp'].';';

	
        //jangan dieksekusi biar bisa didownload berulang-ulang, barangkali ada PC lain diluar yg akses
	//$koneksi -> query("UPDATE pasien_baru set isDownload = 'YES' where id = '".$row["id"]."'");
} 

?>