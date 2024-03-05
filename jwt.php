<?php 
function createAccessToken($payload=null){
	$key_access = 'access_token_ytb';

	if(empty($payload)){
		return null;
	}

	$time = time();

	$payload['iat']= $time;
	$payload['exp']= $time + (60*60*2);

	return signJWT($payload,$key_access);
}

function createRefreshToken($payload=null){
	$key_refresh = 'refresh_token_ytb';

	if(empty($payload)){
		return null;
	}

	$time = time();

	$payload['iat']= $time;
	$payload['exp']= $time + (60*60*24*6);

	return signJWT($payload,$key_refresh);
	
}

function signJWT($payload=null, $key=null){
	$alg = 'sha256';

	// json encode
	$headerJson = json_encode(['typ' => 'JWT', 'alg' => $alg]);
	$payloadJson = json_encode($payload);

	// mã hóa base64
	$base64Header = base64_encode($headerJson);
	$base64Payload = base64_encode($payloadJson);

	// thay thế ký tự trong chuỗi đã mã hóa base64
	$header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
	$payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

	// mã hóa signature
	$msg=$header . "." . $payload;
	$signatureJson = hash_hmac($alg, $msg, $key, true);

	// mã hóa base64 signature
	$base64Signature = base64_encode($signatureJson);

	// thay thế ký tự trong chuỗi đã mã hóa signature
	$signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

	// nối header, payload, signature bằng dấu "."
	$jwt = $header . "." . $payload . "." . $signature;
	return $jwt;
}
function verifyAccessToken($token=null){
	$key_access = 'access_token_ytb';
	return verifyJWT($token, $key_access);
}
function verifyRefreshToken($token=null){
	$key_refresh = 'refresh_token_ytb';
	return verifyJWT($token, $key_refresh);
}
function verifyJWT($token, $key){
	$data=[];
  
	// kiểm tra nếu token không có
  	if (empty($token)) {
		$data['err']=true;
		$data['msg']='Chưa có token';
		return $data;
	}
  
	// tách token thành mảng ['header','payload','signature']
	$detached = explode('.', $token);

	// kiểm tra nếu token không đúng định dạng
 	if (count($detached) != 3) {
 		http_response_code(403);
		$data['err']=true;
		$data['msg']='Token không đúng định dạng';
		return $data;
	}

	// lấy payload trong token
	$base64Payload = $detached[1];

	// giải mã base64 
	$payloadJson = base64_decode($base64Payload);

	// json decode
	$payload = json_decode($payloadJson, true);

	// tạo token mới bằng dữ liệu token cũ
	$jwt = signJWT($payload,$key);

	// kiểm tra nếu token không trùng khớp
	if ($token != $jwt) {
		http_response_code(403);
		$data['err']=true;
		$data['access1']=$token;
		$data['access2']=$jwt;
		$data['msg']='Token không trùng khớp';
		return $data;
	}

	// kiểm tra nếu token hết hạn
	$time = time();
	if ($payload['exp'] < $time) {
		http_response_code(401);
		$data['err']=true;
		$data['msg']='Token đã hết hạn';
		return $data;
	}
	// token hợp lệ
	$data['err']=false;
	$data['user']=$payload;
	return $data;
}
?>