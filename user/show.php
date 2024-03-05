<?php 
	include('../connect.php');
	include('../library.php');
	include('../jwt.php');
	
	$res = [
		'totalUser' => '',
        'totalPage' => '',
        'page' => '',
        'limit' => '',
		'userList'=> [],
	];
	$params = '';
	$type = $_GET['_type'] ? $_GET['_type'] : '';
	$search_type = $_GET['_search_type'] ? $_GET['_search_type'] : '';
	$keyword = $_GET['_keyword'] ? $_GET['_keyword'] : '';
    $limit = isset($_GET['_limit']) && $_GET['_limit'] != '' ? (int)$_GET['_limit'] : 10;
    $page = isset($_GET['_page']) && $_GET['_page'] != '' ? (int)$_GET['_page'] : 1;
    
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
    	$params="WHERE user_id='$user_id'";
    }else if ($type=="search" && $search_type=='user_name') {
    	$params="WHERE $search_type LIKE '%$keyword%'";
    }else if ($type=="search" && $search_type!='user_name') {
    	$params="WHERE $search_type LIKE '%$keyword%'";
    }
    else if ($type=="user_id" && $keyword) {
    	$params="WHERE user_id='$keyword'";
    }

    $sql = "SELECT user_id FROM users ".$params;
	$rl = mysqli_query($conn, $sql);
	$res['totalUser'] = mysqli_num_rows($rl);
	$res['limit'] = $limit;
    $res['page'] = $page;
    $res['totalPage'] = ceil($res['totalUser'] / $res['limit']);
    $start = ($res['page'] - 1) * $res['limit'];

	$sql2 = "SELECT * FROM users ".$params." ORDER BY user_id DESC LIMIT $start, $limit";
	$rl2 = mysqli_query($conn, $sql2);

	while ( $row = mysqli_fetch_assoc($rl2) ) {
		$user_id = $row['user_id'];
		$sql3 = "SELECT COUNT(subscribe_id) as total_subscribe FROM subscriptions WHERE subscribed_to_id='$user_id'";
		$rl3 = mysqli_query($conn, $sql3);
		$subscribe = mysqli_fetch_assoc($rl3);
		$user_name = $row['user_name'];
		$user_avatar = $row['user_avatar'];
		$user_tag = $row['user_tag'];
		$user_email = $row['user_email'];
		$user_des = $row['user_des'];
		$user_total_subscribe = $row['user_total_subscribe'];
		$user_created_at = $row['user_created_at'];
		$uuser_updated_at = $row['uuser_updated_at'];
		array_push($res['userList'], [
			'user_id' => $user_id,
			'user_name' => $user_name,
			'user_avatar' => $user_avatar ? URLImgUser().$user_avatar : '',
			'user_tag' => $user_tag,
			'user_email' => $user_email,
			'user_des' => $user_des,
			'user_total_subscribe' => $subscribe['total_subscribe'],
			'user_created_at' => $user_created_at,
			'user_updated_at' => $user_updated_at,
		]);
	}

	echo json_encode($res);
?>