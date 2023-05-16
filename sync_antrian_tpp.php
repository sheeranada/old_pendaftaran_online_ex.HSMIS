<?php 
session_start();
require("config.php");


$query = $koneksi -> query("select * from antrian_tpp where tgl >= curdate() order by tgl ASC");
while($row = $query -> fetch_array()){
	echo $row['no'].'@'.$row['tgl'].'@'.$row['no_kartu_bpjs'].'@'.$row['kode_booking'].';';
}

?>