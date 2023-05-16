<?php
     
  
     	$main_hosting = "https://rsreksawaluya.com";      
	$host = "localhost";	
	//$username = "jkn_root";
	//$password = "berlian123";
	$dbName = "jkn_online";
	$username = "jkn_mobile";
	$password = "reksawaluya321";
	


	//mysql_connect($host, $username, $password) or die ("Database tidak dapat diakses !");	
	//mysql_select_db($dbName);
	
	$koneksi = mysqli_connect($host, $username, $password, $dbName);
        if(mysqli_connect_error()){ echo "Koneksi database gagal : " . mysqli_connect_error(); }
    
    
    
    function getMainURL(){
	    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	    $var = preg_split("#/#", $actual_link); 
	    $go = '';
	    for($i=0;$i<= count($var)-2;$i++){ $go = $go.$var[$i].'/';	}
	    return $go;
    }



	function invertDate($tgl) {
		$t = explode("-", $tgl);
		return $t[2].'-'.$t[1].'-'.$t[0];
	}

	

	
?>