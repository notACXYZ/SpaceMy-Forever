<?php $conn = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']); ?>
<?php
function validateCaptcha($privatekey, $response) {
	$responseData = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$privatekey.'&response='.$response));
	return $responseData->success;
}

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
?>
