<?php

function invertDate($tgl) {
	$t = explode("-", $tgl);
	return $t[2].'-'.$t[1].'-'.$t[0];
}


require("config.php");
session_start();	



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


$poli = $_SESSION['poli'];
$nama_dokter = $_SESSION['dokter'];
$index_hari = $_SESSION['index_hari'];
$tanggal = $_SESSION['tanggal'];

$avg_service_time = 3;
$result = $koneksi -> query("select * from poli where poli = '$poli' LIMIT 1");
if($row = $result -> fetch_array()){ $avg_service_time = $row['avg_service_time']; }


$max_booking = 3; //booking maks hingga 3 hari kedepan



?>


<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta name="viewport" content="width=device-width; initial-scale=0.9; maximum-scale=0.9;">
<link REL="SHORTCUT ICON" HREF="images/icon.png">
<title>Pendaftaran Online</title>
</head>

<body bgcolor="#C0C0C0">
<table border="0" width="100%" height="100%">
	<tr>
		<td height="308" width="100%">
		<div align="center">
		<img border="0" src="images/logo.png" width="308" height="113">
		<table border="0" width="<?php echo $scale; ?>%" height="280" bgcolor="#FFFFFF" cellspacing="0" cellpadding="0" background="images/bg_login.png" style="background-position: center top; text-align: center; background-image:url('images/bg_login.png')">
		<tr>
		<td height="308" width="100%" valign="top">
		<div align="center">			
			<table border="0" width="100%" height="280" bgcolor="#FFFFFF" background="images/bg_login.png" align="center">
					
					<font color="#FFFFFF"><br>
					*) Harus sudah konfirmasi 30 menit sebelum jam periksa<br>
					*) Apabila terlambat harus antri dari awal.</font><br>
&nbsp;<table border="1" width="80%">
						<tr>
							<td align="center" bgcolor="#666666" colspan="2" >
							<font color="#FFFFFF" ><b>
							
							<?php	
								echo $nama_dokter.'<br>';				
								echo 'Periksa : ';		
								$hari = '';
								if($index_hari%7==0){ $hari = 'Minggu'; }
								else if($index_hari%7==1){ $hari = 'Senin'; }
								else if($index_hari%7==2){ $hari = 'Selasa'; }
								else if($index_hari%7==3){ $hari = 'Rabu'; }
								else if($index_hari%7==4){ $hari = 'Kamis'; }
								else if($index_hari%7==5){ $hari = 'Jumat'; }
								else if($index_hari%7==6){ $hari = 'Sabtu'; }
								echo $hari.', '.$tanggal;
							?>
							
							</b></font></td>
						</tr>
						
						<?php
						
						
								echo '<tr>';	
								echo '<td align="center"><font color="#FFFFFF">No Antrian</font></td>';
								echo '<td align="center"><font color="#FFFFFF">Pilih Jam Diperiksa</font></td>';
								echo '</tr>';	
						
							$no = 1;
							$result = $koneksi -> query("select * from tdok_jadwal where nama = '$nama_dokter' and index_hari = '$index_hari' order by jam_mulai ASC");
							while($row = $result -> fetch_array()){ 
								$lama_praktek = $row['lama'];
								$jam_mulai = strtotime($row['jam_mulai']);
								$jumlah_px_one_slot = $row['jumlah_pasien'];
								
								for($i=1;$i<=$lama_praktek;$i++){
									$jam_nomor = $jam_mulai;
									for($j=1;$j<=$jumlah_px_one_slot;$j++){
									
									$isBooked = false;
									$resultx = $koneksi -> query("select * from antrian_online where dokter = '$nama_dokter' and tgl = '".invertDate($tanggal)."' and no_online = '$no' LIMIT 1");
									if($rowx = $resultx -> fetch_array()){ $isBooked = true; }


									if($isBooked==false){
										echo '<tr>';
										echo '<td align="center" height="50"><font color="#FFFFFF" size="4">L-'.$no.'</font></td>';
										echo '<td align="center" height="50"><a style="color: #FFFFFF; text-decoration: underline" href="cek_daftar.php?no='.$no.'&jam_nomor='.$jam_nomor.'">'.date('H:i',$jam_nomor).'</a></td>';
										echo '</tr>';	
									}else{
										echo '<tr>';
										echo '<td align="center" height="50"><font color="#AAAACC" size="3">L-'.$no.'</font></td>';
										echo '<td align="center" height="50"><font color="#AAAACC" size="3">(Booked)</font></td>';
										echo '</tr>';										
									
									}
									
									$no = $no + 1;	
									$jam_nomor = strtotime("+".$avg_service_time." minutes",$jam_nomor);										
								    $find = true;	
	
								 }
								 $jam_mulai = strtotime("+1 hours", $jam_mulai);								
							 }
								
							}
							
						
  							  if($find==false){
									echo '<tr>';
									echo '<td align="center">Tidak ada jadwal praktek untuk '.$max_booking.' hari kedepan, silahkan hubungi Customer Service</td>';
									echo '</tr>';
							  }
							
						
						?>
						
						
						
						
					</table>
					
					<p><font color="#FFFFFF">*) Harus sudah konfirmasi 30 menit 
					sebelum jam periksa<br>
					*) Apabila terlambat harus antri dari awal.</font><br>
					<a href="pilih_hari.php"><img border="0" src="images/back.png" width="129" height="41"></a>
					<a href="login.php"><img border="0" src="images/home.png" width="129" height="41"></a><br>
&nbsp;</td>
				</tr>
			</table>
		</div>
		</td>
	</tr>
</table>

</body>

</html>
