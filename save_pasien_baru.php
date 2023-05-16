<?php

session_start();	
require("config.php");


$t1 = $_SESSION['t1']; 
$t2 = $_SESSION['t2'];
$t3 = $_SESSION['t3']; 
$t4 = $_SESSION['t4']; 
$t5 = $_SESSION['t5']; 
$t6 = $_SESSION['t6']; 
$t7 = $_SESSION['t7']; 
$t8 = $_SESSION['t8']; 
$t9 = $_SESSION['t9']; 
$t10 = $_SESSION['t10']; 


$id = 1; 
$query = $koneksi -> query("select * from pasien_baru ORDER BY id DESC LIMIT 1");
while($row = $query -> fetch_array()){ $id = $row['id'] + 1; }


$query = mysqli_query($koneksi,"INSERT INTO pasien_baru values ('$id', '$t1', '$t2', '$t4', '$t3', '$t5', '$t6', '$t7', '', '$t9', '$t10', '' )");
$_SESSION['id'] = $id; 
$_SESSION['nama_pasien'] = $t1; 

header('location:pilih_poli.php');


?>
