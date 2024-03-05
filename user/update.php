<?php 
	include('../connect.php');
	include('../jwt.php');
		$res = [];
		$user_name = $_POST['_user_name'] ? $_POST['_user_name'] : '';
		$user_tag =  $_POST['_user_tag'] ? $_POST['_user_tag'] : '';
		$user_des =  $_POST['_user_des'] ? $_POST['_user_des'] : '';
	    $headers = apache_request_headers();
	    $token = $headers['access_token'];
	    $token = str_replace('Bearer ', '', $token);
	    $verify = verifyAccessToken($token);

	    if ($verify['err']) {
	        array_push($res, ['error'=>true, 'message'=>$verify['msg']]);
	        echo json_encode($res);
	        die();
	    }
	    $user_id = $verify['user']['user_id'];
	    
	    if ($user_name == '' || $user_tag == '' ) {
	    	array_push($res, ['error'=> true,'message'=> 'Bạn chưa nhập đủ thông tin']);
        	echo json_encode($res);
	    	die();
	    }
	    
	    $sqlCheckTag = "SELECT user_id FROM users WHERE user_tag='$user_tag' AND user_id!='$user_id' LIMIT 1";
	    $rlCheck = mysqli_query($conn,$sqlCheckTag);
	    $check = mysqli_num_rows($rlCheck);

	    if ($check > 0) {
	    	array_push($res, ['error'=> true,'message'=> 'Tên người dùng đã tồn tại']);
        	echo json_encode($res);
	    	die();
	    }

    	$sqlUpdate = "UPDATE users SET user_name='$user_name', user_tag='$user_tag', user_des='$user_des'WHERE user_id='$user_id'";
    	$rlUpdate = mysqli_query($conn, $sqlUpdate);
    	
    	array_push($res, ['error'=> false,'message'=> 'Cập nhật thành công !']);
    	echo json_encode($res);
	    
?>