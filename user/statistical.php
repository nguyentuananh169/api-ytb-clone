<?php 
	include('../connect.php');
	include('../library.php');
	include('../jwt.php');
	
	$res = [];
	$params = '';
	$type = $_GET['_type'] ? $_GET['_type'] : '';
	$id = $_GET['_id'] ? $_GET['_id'] : '';
    
    if ($type=='get_by_token') {
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
    }else if ($type=="get_by_id" && $id!='') {
    	$user_id=$id;
    }

	$sql = "SELECT * FROM users WHERE user_id='$user_id'";
	$rl = mysqli_query($conn, $sql);
	$user = mysqli_fetch_assoc($rl);

	$sql2 = "SELECT video_views FROM videos WHERE user_id='$user_id'";
	$rl2 = mysqli_query($conn, $sql2);
	$total_video = mysqli_num_rows($rl2);
	$total_views = 0;
	while ($row = mysqli_fetch_assoc($rl2)) {
		$total_views+=$row['video_views'];
	}

	$sql3 = "SELECT COUNT(subscribe_id) as total_subscribe FROM subscriptions WHERE subscribed_to_id='$user_id'";
	$rl3 = mysqli_query($conn, $sql3);
	$subscribe = mysqli_fetch_assoc($rl3);

	array_push($res, [
		'user_id' => $user['user_id'],
		'user_name' => $user['user_name'],
		'user_avatar' => $user['user_avatar'] ? URLImgUser().$user['user_avatar'] : '',
		'user_banner' => $user['user_banner'] ? URLImgUserBanner().$user['user_banner'] : '',
		'user_tag' => $user['user_tag'],
		'user_email' => $user['user_email'],
		'user_des' => $user['user_des'],
		'user_total_subscribe' => $subscribe['total_subscribe'],
		'user_total_video' => $total_video,
		'user_total_views' => $total_views,
		'user_created_at' => $user['user_created_at'],
		'user_updated_at' => $user['user_updated_at'],
		
	]);
	

	echo json_encode($res);
?>