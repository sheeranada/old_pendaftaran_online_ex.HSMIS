<?php

require("config.php");
session_start();	

$scale = 25;
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

$poli =	$_SESSION['poli'];

?>


<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta name="viewport" content="width=device-width; initial-scale=0.9; maximum-scale=0.9;">
<link REL="SHORTCUT ICON" HREF="images/icon.png">
<title>Pendaftaran Online</title>
</head>

<body>
<table border="0" width="100%" height="100%"   >
    	<tr>
		<td height="308" width="100%">
		<div align="center">
		<img border="0" src="images/logo.png" width="308" height="113">
		<table border="0" width="<?php echo $scale; ?>%" height="280" cellspacing="0" cellpadding="0">
		<tr>
		<td height="308" width="100%" valign="top">
		<div align="center">			
		<table border="0" width="100%" height="280" bgcolor="#FFFFFF" align="center">
						
						<table border="0" width="100%" height="158">
							
							<?php
								$result = $koneksi -> query("select nama from dokter where poli = '$poli' ORDER BY nama ASC");
								while($row = $result -> fetch_array()){
									echo '<tr><td>';
									echo '<font size="4"><a href="cek_dokter.php?dokter='.$row['nama'].'"><span style="color: #000000; text-decoration: underline">'.$row['nama'].'</span></a><br><br></font>';
									echo '</td></tr>';
								}
							?>
							
							</table>
					<p>
					<a href="pilih_poli.php"><img border="0" src="images/back.png" width="129" height="41"></a>
					<a href="login.php"><img border="0" src="images/logout.png" width="129" height="41"></a>
					</td>
				</tr>
			</table>
		</div>
		</td>
	</tr>
</table>

</body>

</html>