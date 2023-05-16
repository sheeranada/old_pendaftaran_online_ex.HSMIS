<?php

require("config.php");
require("tools.php");

$username = 'bukan_saya';
$password = 'gagal';

foreach (getallheaders() as $name => $value) {
    if(strtolower($name)=='x-username'){ $username = $value; }
    if(strtolower($name)=='x-password'){ $password = $value; }
}


if($username!='jkn_mobile'){
    echo '{"metadata":{"message":"Username Tidak Sesuai","code":201}}';
    exit();
}

if($password!='reksawaluya321'){
    echo '{"metadata":{"message":"Password Tidak Sesuai","code":201}}';
    exit();
}


$seed = date("Y-m-d h:m:s");
$token = get_jwtx();

mysqli_query($koneksi, "DELETE FROM token_active where expired < now()");
mysqli_query($koneksi, "INSERT INTO token_active values ('$token', DATE_ADD(now(), INTERVAL 10 MINUTE) )");
echo '{"response": { "token" : "'.$token.'" }, "metadata": {"message": "Ok","code": 200}}';

?>
