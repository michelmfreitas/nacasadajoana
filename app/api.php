<?php
//$url = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/";
$url = "https://ws.pagseguro.uol.com.br/v2/transactions/";
$dados = $_POST['dados'];
$data = http_build_query($dados);
//echo $data;
$curl = curl_init($url);

curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

$output = curl_exec($curl);
curl_close($curl);
//echo $output;
//exit();
$xml = simplexml_load_string($output);
echo json_encode($xml);
?>