
<option value="">PILIH KELURAHAN</option>
<?php

 require('config.php');
 $kecamatan = $_POST['kecamatan'];
 $result = $koneksi -> query("SELECT * FROM kelurahan where kecamatan = '$kecamatan' ORDER BY kelurahan ASC ");
 while($row = $result -> fetch_array()){	
  
?>
 
<option value="<?php echo $row['kelurahan'] ?>"><?php echo $row['kelurahan']; ?></option>
 
<?php } ?>