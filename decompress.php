<?php 

include 'LZCompressor/LZString.php';
include 'LZCompressor/LZReverseDictionary.php';
include 'LZCompressor/LZContext.php';
include 'LZCompressor/LZData.php';
include 'LZCompressor/LZUtil.php';
include 'LZCompressor/LZUtil16.php';

function DecompressData($key, $string_data){
    $decrypt_data = stringDecrypt($key, $string_data);
    return decompress($decrypt_data);
}


function stringDecrypt($key, $string){
  $encrypt_method = 'AES-256-CBC';
  $key_hash = hex2bin(hash('sha256', $key));
  $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
  $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
  return $output;
}
    
    
function decompress($string){
    return \LZCompressor\LZString::decompressFromEncodedURIComponent($string);
}


?>