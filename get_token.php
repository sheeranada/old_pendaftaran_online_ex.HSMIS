<?php

require("config.php");


$data = json_decode(file_get_contents('php://input'), true);

if(!empty($data)){
    $username = $data["username"];
    $password =  $data["password"];
}else{
    echo '{"metadata":{"message":"INVALID DATA","code":"500"}}';
    exit();
}


if($username!='jkn_mobile'){
    echo '{"metadata":{"message":"ACCESS DENIED","code":"500"}}';
    exit();
}

if($password!='1234567890'){
    echo '{"metadata":{"message":"ACCESS DENIED","code":"500"}}';
    exit();
}


$seed = date("Y-m-d h:m:s");
$token = 'INVALID';
$query = $koneksi -> query("select hex(AES_ENCRYPT('$seed','tokenRSRL')) as cd");
while($row = $query -> fetch_array()){		
  	$token = substr($row['cd'],-12);
}



mysqli_query($koneksi, "DELETE FROM token_active where expired < now()");
mysqli_query($koneksi, "INSERT INTO token_active values ('$token', DATE_ADD(now(), INTERVAL 3 MINUTE) )");
echo '{"response": { "token" : "'.$token.'" }, "metadata": {"message": "Ok","code": 200}}';

?>
