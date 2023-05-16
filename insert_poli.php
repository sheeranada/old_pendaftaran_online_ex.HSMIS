<?php

require("config.php");

$poli = $_GET['poli'];
$polix = explode(",",$poli);

for($i=0;$i<=count($polix)-1;$i++){
	$dt = explode("@" , $polix[$i]);	
	
	if(strlen($dt[0])>3&&strlen($dt[2])>0&&strlen($dt[3])>0){
	    echo 'Insert : '.$dt[0];
	    mysqli_query($koneksi, "DELETE FROM poli where poli = '$dt[0]'");
		$query = mysqli_query($koneksi, "INSERT INTO poli values ('$dt[0]','$dt[1]','$dt[2]', '$dt[3]', '', '', '', '')"); 		
		$query = $koneksi -> query("select * from poli where poli = '$dt[0]' LIMIT 1");
		if($row = $query){ echo ' = SUKSES';	}
		else{ echo ' = GAGAL';}
		echo '<br>';
	}
}
?>