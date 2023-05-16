<?php

require("config.php");

$nama_dokter = $_GET['nama_dokter'];
mysqli_query($koneksi, "DELETE FROM tdok_jadwal where nama = '$nama_dokter' ");

$full_data = $_GET['full_data'];
$jadwal = explode("$",$full_data);
for($i=0;$i<=count($jadwal)-1;$i++){
	$data = explode(";", $jadwal[$i]);
	if(strlen($nama_dokter)>3&&strlen($data[0])>0){	
		$query = mysqli_query($koneksi, "INSERT INTO tdok_jadwal values ('$nama_dokter', '$data[0]', '$data[1]', '$data[2]', '', '$data[3]', '', '', '', '', '', '', '', '', '', '' )");
	}
}

?>
