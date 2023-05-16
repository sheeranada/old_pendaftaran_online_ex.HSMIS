<?php

require("config.php");
session_start();	

$max_booking = 3; //booking maks hingga 3 hari kedepan

$scale = 30;
$view = 'desktop';
if(isset($_SESSION['view']) && !empty($_SESSION['view'])) { $view = $_SESSION['view']; } 
if($view!='desktop'){ $scale = 100;  }


$id = '';
if(isset($_SESSION['id']) && !empty($_SESSION['id'])) { $id = $_SESSION['id']; }
$nama_pasien = '';
if(isset($_SESSION['nama_pasien']) && !empty($_SESSION['nama_pasien'])) { $nama_pasien = $_SESSION['nama_pasien']; }

if(strlen($id)==0||strlen($nama_pasien)==0){
	header('location:login.php');
}


?>


<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta name="viewport" content="width=device-width; initial-scale=0.9; maximum-scale=0.9;">
<link REL="SHORTCUT ICON" HREF="images/icon.png">
<title>Pendaftaran Online</title>
</head>

<body>
<div align="center">
&nbsp;</div>
<table border="0" width="100%" height="100%" style="background-position: center top; text-align: center">
	<tr>
		<td height="308" width="1080" valign="top">
		<div align="center">
			<img border="0" src="images/logo.png" width="308" height="113">
			<table border="1" style="border-collapse: collapse" bordercolor="#CCCCCC" bordercolordark="#DBDBDB" width="<?php echo $scale; ?>%" height="328">
				<tr>
					<td rowspan="3" align="center">
					
					&nbsp;<table border="0" width="100%">
						<tr>
							<td width="94">No RM</td>
							<td width="3">:</td>
							<td><?php echo $_SESSION['id']; ?></td>
						</tr>
						<tr>
							<td width="94">Nama</td>
							<td width="3">:</td>
							<td><?php echo $_SESSION['nama_pasien']; ?></td>
						</tr>
						<tr>
							<td width="94">Poli</td>
							<td width="3">:</td>
							<td><?php echo $_SESSION['poli']; ?></td>
						</tr>
						<tr>
							<td width="94">Dokter</td>
							<td width="3">:</td>
							<td><?php echo $_SESSION['dokter']; ?></td>
						</tr>
						<tr>
							<td width="94">Hari</td>
							<td width="3">:</td>
							<td>
							<?php 
							
								$index_hari = $_SESSION['index_hari'];
								$hari = '';
								if($index_hari%7==0){ $hari = 'Minggu'; }
								else if($index_hari%7==1){ $hari = 'Senin'; }
								else if($index_hari%7==2){ $hari = 'Selasa'; }
								else if($index_hari%7==3){ $hari = 'Rabu'; }
								else if($index_hari%7==4){ $hari = 'Kamis'; }
								else if($index_hari%7==5){ $hari = 'Jumat'; }
								else if($index_hari%7==6){ $hari = 'Sabtu'; }
								echo $hari;
							?>
							</td>
						</tr>
						<tr>
							<td width="94">Tgl Periksa</td>
							<td width="3">:</td>
							<td><?php echo $_SESSION['tanggal']; ?></td>
						</tr>
						<tr>
							<td width="94">Jam Praktek</td>
							<td width="3">:</td>
							<td><?php echo $_SESSION['jam']; ?></td>
						</tr>

					</table>
					<form method="POST" action="insert_daftar.php">
						<p>
						<input type="submit" value="Daftarkan Sekarang" name="B1" style="width: 208; height: 38"></p>
					</form>
					<a href="pilih_hari.php"><img border="0" src="images/back.png" width="129" height="41"></a>
					<a href="login.php"><img border="0" src="images/home.png" width="129" height="41"></a></td>
				</tr>
			</table>
		</div>
		</td>
	</tr>
</table>

</body>

</html>