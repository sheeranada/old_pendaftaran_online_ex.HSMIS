<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.google.com/");
curl_setopt($ch, CURLOPT_HEADER, 0);
$output = curl_exec($ch);
curl_close($ch);

echo '<br>'.$output;

?>
