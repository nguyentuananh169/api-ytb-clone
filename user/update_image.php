<?php 
	include('../connect.php');
	include('../jwt.php');
	include('../library.php');
		$res = [];
		$type = $_POST['_type'] ? $_POST['_type'] : '';
		$avatar =  $_FILES['_avatar'] ? $_FILES['_avatar'] : '';
		$banner =  $_FILES['_banner'] ? $_FILES['_banner'] : '';
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
	    $time = time();
	    
	    if ($type == 'avatar' && $avatar['name'] !== '' ) {
	    	if ($avatar['type'] != 'image/png' && $avatar['type'] != 'image/jpeg') {
		        array_push($res, ['error'=> true, 'message'=> 'Hình ảnh bạn nhập không đúng định dạng (PNG, JPEG)']);
		        echo json_encode($res);
		        die();
		    }

		    $sql_avatar = "SELECT user_avatar FROM users WHERE user_id='$user_id'";
		    $rl_avatar = mysqli_query($conn,$sql_avatar);
		    $data = mysqli_fetch_assoc($rl_avatar);

		    if (!empty($data['user_avatar'])) {
		    	unlink('../images/user/'.$data['user_avatar']);
		    }

		    $file_name = $time.$avatar['name'];
		    $sql = "UPDATE users SET user_avatar='$file_name' WHERE user_id='$user_id'";
		    $rl = mysqli_query($conn, $sql);
		    move_uploaded_file($avatar['tmp_name'], '../images/user/'.$file_name);

		    array_push($res, ['error'=> false,'message'=> 'Cập nhật thành công ','image'=>URLImgUser().$file_name]);
    		echo json_encode($res);
    		die();
	    }

	    if ($type == 'banner' && $banner['name'] !== '' ) {
	    	if ($banner['type'] != 'image/png' && $banner['type'] != 'image/jpeg') {
		        array_push($res, ['error'=> true, 'message'=> 'Hình ảnh bạn nhập không đúng định dạng (PNG, JPEG)']);
		        echo json_encode($res);
		        die();
		    }

		    $sql_banner = "SELECT user_banner FROM users WHERE user_id='$user_id'";
		    $rl_banner = mysqli_query($conn,$sql_banner);
		    $data = mysqli_fetch_assoc($rl_banner);

		    if (!empty($data['user_banner'])) {
		    	unlink('../images/banner/'.$data['user_banner']);
		    }

		    $file_name = $time.$banner['name'];
		    $sql = "UPDATE users SET user_banner='$file_name' WHERE user_id='$user_id'";
		    $rl = mysqli_query($conn, $sql);
		    move_uploaded_file($banner['tmp_name'], '../images/banner/'.$file_name);

		    array_push($res, ['error'=> false,'message'=> 'Cập nhật thành công ','image'=>URLImgUserBanner().$file_name]);
    		echo json_encode($res);
    		die();
	    }

	    array_push($res, ['error'=> true,'message'=> 'Chưa đủ thông tin ']);
    	echo json_encode($res);
    	
	    
?>