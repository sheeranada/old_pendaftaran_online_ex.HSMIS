<?php

session_start();	
require('config.php');

$sql = '';
if(isset($_GET['sql']) && !empty($_GET['sql'])){ $sql = $_GET['sql']; }

if(strlen($sql)==0){
  exit();
}else{
  $koneksi -> query($sql);	
}


?>