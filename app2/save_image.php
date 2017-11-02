<?php
//$contador = 1;
$name = '.pdf';
$mult = mt_rand();
$mix = $mult.$name;


if(!empty($_POST['data'])){
$data = base64_decode($_POST['data']);
// print_r($data);
	file_put_contents( "assets/".$mix, $data );
} else {
	echo "No Data Sent";
}

exit();


?>
