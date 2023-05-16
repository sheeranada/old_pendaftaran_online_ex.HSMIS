<?php
require("config.php");
session_start();	


$_SESSION['id'] = ""; 
$_SESSION['nama_pasien'] = "";
$_SESSION['poli'] = "";
$_SESSION['dokter'] = "";
$_SESSION['index_hari'] = ""; 
$_SESSION['tanggal'] = "";
$_SESSION['no'] = "";
$_SESSION['jam_nomor'] = "";
$_SESSION['no_hp'] = "";


$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
{ 
     $_SESSION['view'] = 'mobile';
}
else{
     $_SESSION['view'] = 'desktop';
}


$scale = 30;
$view = 'desktop';
if(isset($_SESSION['view']) && !empty($_SESSION['view'])) { $view = $_SESSION['view']; } 
if($view!='desktop'){ $scale = 100;  }

$m = "";
if(isset($_GET['m']) && !empty($_GET['m'])) { $m = $_GET['m']; } 

?>

<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta name="viewport" content="width=device-width; initial-scale=0.9; maximum-scale=0.9;">
<link REL="SHORTCUT ICON" HREF="images/icon.png">
<title>Pendaftaran Online</title>
</head>

<body>
<table border="0" width="100%" height="100%"  > 
	<tr>
		<td height="308" width="100%" valign="top">
		<div align="center">
			<table border="0" width="<?php echo $scale; ?>%" height="280" cellspacing="0" cellpadding="0" >
				<tr>
					<td rowspan="1">
					<form method="POST" action="cek_login.php" autocomplete="off">
						<p align="center"><b><font size="3">
						</font></b><img border="0" src="images/logo.png" width="288" height="108"><b><font size="5"><br>				
						<?php	
							if($m=='denied') { echo '<font color="#FF1010">Akses ditolak<br>No RM &amp; Tanggal Lahir Salah<br></font>'; }
							else if($m=='wrong_id') { echo '<font color="#FF1010">Akses ditolak<br>Masukkan No RM dengan benar<br></font>'; }
							else if($m=='error_simpan_data') { echo '<font color="#FF1010">Ada kesalahan dalam menyimpan data</font>'; }

							else{ echo 'Selamat Datang'; }
						?>

						</font></b><p align="center">Pendaftaran ini khusus untuk pasien <br>
						yang sudah pernah mendaftar <br>
						di RS Reksa Waluya <br><br>
						Silahkan memasukkan No Rekam Medis <br>
						dan Tanggal lahir (format : ddmmyyyy)</p>
						
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td height="25" align="center">
								No Rekam Medis :<br>
								<font size="3" color="#FFFFFF">
								<input type="text" name="T1" placeholder="No. Rekam Medis" size="30" style="width: 150; height: 32; text-align:center"></font></td>
							</tr>
							<tr>
								<td align="center"><br>
								Tanggal Lahir :<br>
								<font size="3" color="#FFFFFF">
								<input type="password" name="T2" placeholder="Tgl Lahir (ddmmyyyy)" size="30" style="width: 150; height: 32; text-align:center"></font></td>
							</tr>
							<tr>
								<td align="center"><br>
								<input type="submit" value="Login" name="B1" style="width: 100; height: 42"></td>
							</tr>						
						</table>							
					</form>
					<br><br>
					
					 <table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" >					
							<form method="POST" action="form_pasien_baru.php">
								<p align="center">
							<input type="submit" value="Daftar Pasien Baru (Belum Pernah Berkunjung)" name="B2" style="width: 319; height: 46"></p>
								<p align="center">
					
								<?php echo '<a href="'.$main_hosting.'">'; ?>
								<img border="0" src="images/home.png" width="129" height="41"></a></a></p>
							</form>
							</td>
						</tr>
					</table> 
					</td>
				</tr>				
			</table>
		</div>
		</td>
	</tr>
</table>

</body>

</html>