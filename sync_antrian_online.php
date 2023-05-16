<?php 
session_start();
require("config.php");

$txt = "";
$query = $koneksi -> query("select * from antrian_online where tgl >= now() order by jam ASC, dokter ASC");
while($row = $query -> fetch_array()){
	echo $row['dokter'].'@'.$row['tgl'].'@'.$row['jam'].'@'.$row['no_online'].'@'.$row['cib'].'@'.$row['index_hari'].'@'.$row['poli'].';';
}

?>