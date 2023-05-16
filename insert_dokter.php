<?php

require("config.php");

$dokter = $_GET['dokter'];
$dokterx = explode("$",$dokter);

for($i=0;$i<=count($dokterx)-1;$i++){
	$data = explode(";", $dokterx[$i]);
	if(strlen($data[0])>3&&strlen($data[3])>0){
	    ///periksa apakah quota_prioriti ada isinya, klo kosong isi dengan settingan terakhir
	    if(strlen($data[2])==0){
	        $result = $koneksi -> query("select quota_pasien_prior_vs_non_prior from dokter where nama = '$data[0]' LIMIT 1");
			if($row = $result -> fetch_array()){
			    $data[2] = $row['quota_pasien_prior_vs_non_prior'];			
			}
	    }
	    
	    //jika masih kosong update by default
	   if(strlen($data[2])==0){ $data[2] = "10#40"; }
	   mysqli_query($koneksi, "DELETE FROM dokter where nama = '$data[0]' ");
	   $query = mysqli_query($koneksi, "INSERT INTO dokter values ('$data[0]', '$data[1]', '$data[2]', '$data[3]' )");
	
         }
}

?>
