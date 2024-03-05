<?php 
	include('../connect.php');
	include('../jwt.php');
	
	$res = [];
	$params = '';
	$keyword = $_GET['_keyword'] ? $_GET['_keyword'] : '';
    
	$headers = apache_request_headers();
    $token = $headers['access_token'];
    $token = str_replace('Bearer ', '', $token);
    $verify = verifyAccessToken($token);
    if (!$verify['err']) {
        $user_id = $verify['user']['user_id'];
    }
   	if ($user_id) {
   		$params="AND user_id!='$user_id'";
   	}

	$sqlCheck = "SELECT user_id FROM users WHERE user_tag='$keyword' $params LIMIT 1";
	$rlCheck = mysqli_query($conn, $sqlCheck);
	$check = mysqli_num_rows($rlCheck);

	if ($check > 0) {
		array_push($res, ['error'=>true, 'message'=>'Tên người dùng đã tồn tại']);
        echo json_encode($res);
        die();
	}
	array_push($res, ['error'=>false, 'message'=>'Tên người dùng không tồn tại']);
    echo json_encode($res);
?>