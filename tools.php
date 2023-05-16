<?php


function get_jwtx(){
  $seed = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9eyJ1aWQiOiI1ZjFlOTYyMGY1NmZmNzNjYTNlMGFmNGEiLCJmdWxsTmFtZSI6IkdhdGV3YXkgU3lzdGVtIiwiZmlyc3ROYW1lIjoiR2F0ZXdheSIsIm1pZGRsZU5hbWUiOiIiLCJsYXN0TmFtZSI6IlN5c3RlbSIsIm1vYmlsZXBob25lIjoiNjI4OTU0MDYxODIwOTAiLCJjb3VudHJ5Q29kZSI6IiIsInJvbGUiOiJjbGllbnQiLCJleHAiOjE1OTYxNTM1OTkwMDAsImlhdCI6MTU5NTkzMTQwMCwiaX";
  $token = "";
  for($i=0;$i<=strlen($seed);$i++){
        $cx = substr($seed, rand(1, strlen($seed) - 1), 1);
  		$token = $token.$cx;
  }
  return $token;
}


function get_data_rujukan($no_rujukan) {
  
   ///REKSO	
   //antrol development
   //$data = '14540';
   //$secretKey = '7iX21C7226';

   //antrol production
   $data = '21344';
   $secretKey = '6hT159AA65';

   date_default_timezone_set('UTC');
   $tStamp = strval(time()-strtotime('1970-01-01 00:00:00'));
   $signature = hash_hmac('sha256', $data."&".$tStamp, $secretKey, true);
   $encodedSignature = base64_encode($signature);
   $request_headers = array(
                    "X-cons-id: ".$data,
                    "X-timestamp: ".$tStamp,
                    "X-signature: ".$encodedSignature
                );

   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://new-api.bpjs-kesehatan.go.id:8080/new-vclaim-rest/Rujukan/".$no_rujukan);  ///URL PRODUCTION
    //curl_setopt($ch, CURLOPT_URL,"https://dvlp.bpjs-kesehatan.go.id/VClaim-Rest/Rujukan/".$no_rujukan);  ///URL DEVELOPMENT
    curl_setopt($ch, CURLOPT_CAINFO, '/path/to/cert/file/cacert.pem');
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
    $server_output = curl_exec($ch);
    curl_close($ch);
    return $server_output; 
} 




?>