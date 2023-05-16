<?php


require("config.php");


$no_rm = $_GET['no_rm'];
$nama = addslashes($_GET['nama']);
$alamat = addslashes($_GET['alamat']);
$tgl_lahir = $_GET['tgl_lahir'];
$no_kartu = $_GET['no_kartu'];

if(strlen($no_rm)>=9&&$nama!='APS'&&strlen($nama)>0){
	$query = mysqli_query($koneksi,"DELETE FROM pasien_pribadi where id = $no_rm");
        //TANPA ENKRIPSI
        $query = mysqli_query($koneksi,"INSERT INTO pasien_pribadi values ($no_rm, '$nama', '$alamat', '$tgl_lahir', '$no_kartu', '', '', '', '', ''  ) ");
	

        if($query) { echo '200'; exit(); }
	else{ echo 'FAILED #1'.mysqli_error($koneksi); exit(); }
}

echo 'FAILED #2';
?>
