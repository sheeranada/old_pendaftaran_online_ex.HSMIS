<?php

require("config.php");
session_start();	

$scale = 50;
$view = 'desktop';
if(isset($_SESSION['view']) && !empty($_SESSION['view'])) { $view = $_SESSION['view']; } 
if($view!='desktop'){ $scale = 100;  }


$max_booking = 3; //booking maks hingga 3 hari kedepan

if($_SESSION['kode_booking']==''){
    header('location:login.php');
	exit();
}


$avg_service_time = 6;
$query = $koneksi -> query("select * from poli where poli = '".$_SESSION['poli']."' LIMIT 1");
while($row = $query -> fetch_array()){ $avg_service_time = $row['avg_service_time']; }
if($avg_service_time<=0){ $avg_service_time = 10;}

$no_urut = $_SESSION['no_online'];
$jam = $_SESSION['jam'];

$add_menit = '+'.($avg_service_time * ($no_urut - 1)).' minutes';
$jam_periksa = date('H:i', strtotime($add_menit, strtotime($jam)));

?>


<html>
<head>

<style>
.alias {cursor: alias;}
.all-scroll {cursor: all-scroll;}
.auto {cursor: auto;}
.cell {cursor: cell;}
.context-menu {cursor: context-menu;}
.col-resize {cursor: col-resize;}
.copy {cursor: copy;}
.pointer {cursor: pointer;}
.crosshair {cursor: crosshair;}
.default {cursor: default;}
.e-resize {cursor: e-resize;}
.ew-resize {cursor: ew-resize;}
.grab {cursor: grab;}
.grabbing {cursor: grabbing;}
.help {cursor: help;}
.move {cursor: move;}
.n-resize {cursor: n-resize;}
.ne-resize {cursor: ne-resize;}
.nesw-resize {cursor: nesw-resize;}
.ns-resize {cursor: ns-resize;}
.nw-resize {cursor: nw-resize;}
.nwse-resize {cursor: nwse-resize;}
.no-drop {cursor: no-drop;}
.none {cursor: none;}
.not-allowed {cursor: not-allowed;}
.pointer {cursor: pointer;}
.progress {cursor: progress;}
.row-resize {cursor: row-resize;}
.s-resize {cursor: s-resize;}
.se-resize {cursor: se-resize;}
.sw-resize {cursor: sw-resize;}
.text {cursor: text;}
.url {cursor: url(myBall.cur),auto;}
.w-resize {cursor: w-resize;}
.wait {cursor: wait;}
.zoom-in {cursor: zoom-in;}
.zoom-out {cursor: zoom-out;}
</style>

<style type="text/css">
   @font-face {
         font-family: "IDAutomationHC39M";
         src: url('IDAutomationHC39M.ttf');
         }
 
   .digital {
         font-family: "IDAutomationHC39M";
         }
</style>

<meta http-equiv="Content-Language" content="en-us">
<meta name="viewport" content="width=device-width; initial-scale=0.9; maximum-scale=0.9;">
<link REL="SHORTCUT ICON" HREF="images/icon.png">
<title>Pendaftaran Online</title>
</head>

<body bgcolor="#C0C0C0">
<table border="0" width="100%" height="80%">
	<tr>
		<td height="308" width="50%">
		<div align="center">
			<table border="0" width="<?php echo $scale; ?>%" height="280" bgcolor="#FFFFFF" cellspacing="0" cellpadding="0">
				<tr>
					<td rowspan="3" align="center">
					
					<img border="0" src="images/logo.png" width="308" height="113"><p>
					<font size="5"><b>BERHASIL</b></font></p>
					<table border="0" width="100%">
						<tr>
							<td width="100">No RM</td>
							<td width="8">:</td>
							<td><?php echo $_SESSION['id']; ?></td>
						</tr>
						<tr>
							<td width="100">Nama</td>
							<td width="8">:</td>
							<td><?php echo $_SESSION['nama_pasien']; ?></td>
						</tr>
						<tr>
							<td width="100">Poli</td>
							<td width="8">:</td>
							<td><?php echo $_SESSION['poli']; ?></td>
						</tr>
						<tr>
							<td width="100">Dokter</td>
							<td width="8">:</td>
							<td><?php echo $_SESSION['dokter']; ?></td>
						</tr>
						<tr>
							<td width="100">Hari</td>
							<td width="8">:</td>
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
							<td width="100">Tgl Periksa</td>
							<td width="8">:</td>
							<td><?php echo $_SESSION['tanggal']; ?></td>
						</tr>
						<tr>
							<td width="100">Jam Mulai</td>
							<td width="8">:</td>
							<td><?php echo $_SESSION['jam']; ?></td>
						</tr>
						<tr>
							<td width="100"><font color="#FF0000">No Antrian</font></td>
							<td width="8"><font color="#FF0000">:</font></td>
							<!-- <td><font color="#FF0000"><?php echo $_SESSION['no_online'].' (Estimasi: '.$jam_periksa.')'; ?></font></td>	-->	
								<td><font color="#FF0000"><?php echo $_SESSION['no_online'] ?></font></td>
						</tr>
					 	<tr>
							<td width="100"><font color="#FF0000">Kode Booking</font></td>
							<td width="8"><font color="#FF0000">:</font></td>
							<td><font color="#FF0000"><?php echo $_SESSION['kode_booking']; ?></font></td>							
						</tr>
						<tr>
							<td colspan="3">
		                    			<p align="center"><font color="#FF0000">Harap hadir dan konfirmasi kedatangan<br>di pendaftaran 30 menit lebih awal</font></td>
						</tr>
						
					</table>
					<?php
						echo '<h1><p class="digital">*'.$_SESSION['kode_booking'].'*</p></h1><br>';
					?>
					
					<a href="login.php">
					<img border="0" src="images/logout.png" width="155" height="48"></a><br>
&nbsp;</td>
				</tr>
			</table>
		</div>
		</td>
	</tr>
</table>

</body>

</html>

