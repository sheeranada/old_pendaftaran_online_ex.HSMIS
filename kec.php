<option value="">PILIH KECAMATAN</option>
<?php
require('config.php'); 
 $kota = $_POST['kota'];
 $result = $koneksi -> query("SELECT * FROM kecamatan where kota = '$kota' ORDER BY kecamatan ASC ");
 while($row = $result -> fetch_array()){									
 
?>
 
<option value="<?php echo $row['kecamatan'] ?>"><?php echo $row['kecamatan']; ?></option>
 
 <?php } ?>