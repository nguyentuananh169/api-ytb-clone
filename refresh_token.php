<?php 
	include('connect.php');
	include('jwt.php');

	$headers = apache_request_headers();
    $refreshToken = $headers['refresh_token'];
    $verify = verifyRefreshToken($refreshToken);

    if ($verify['err']) {
        echo json_encode($verify);
        die();
    }

    $detached = explode('.', $refreshToken);

    // lấy payload trong token
	$base64Payload = $detached[1];

    // giải mã base64 
	$payloadJson = base64_decode($base64Payload);

	// json decode
	$payload = json_decode($payloadJson, true);

    $jwt = createAccessToken($payload);

	echo json_encode([
		'error'=>false,
		'access_token'=> $jwt,]);
?>