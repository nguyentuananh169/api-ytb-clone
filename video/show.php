<?php 
	include('../connect.php');
	include('../library.php');
	include('../jwt.php');
	
	$res = [
		'totalVideo' => '',
        'totalPage' => '',
        'page' => '',
        'limit' => '',
		'videoList'=> [],
	];
	$params = '';
	$sql_limit = '';
	$id = $_GET['id'];
	$type = $_GET['type'] ? $_GET['type'] : '';
	$search_type = $_GET['search_type'] ? $_GET['search_type'] : '';
	$keyword = $_GET['keyword'] ? $_GET['keyword'] : '';
    $limit = $_GET['limit'] ? (int)$_GET['limit'] : 10;
    $page = $_GET['page'] ? (int)$_GET['page']: 1;
    $order_by = $_GET['order_by'] ? $_GET['order_by'] : 'video_id';
    $order_type = $_GET['order_type'] ? $_GET['order_type'] : 'DESC';
    $black_list_name = $_GET['black_list_name'] ? $_GET['black_list_name'] : '';
    $black_list_value = $_GET['black_list_value'] ? $_GET['black_list_value'] : '';
    $is_unlimited = $_GET['is_unlimited']? $_GET['is_unlimited'] : false;
    
    if ($type == 'get_by_token') {
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
	    if ($search_type == 'title' && $keyword !='') {
	    	$params="WHERE video_title LIKE '%$keyword%' AND videos.user_id='$user_id'";
	    }else{
			$params="WHERE videos.user_id='$user_id'";
	    }
    }else if ($type=='category_id' && $id != '') {
    	$params="WHERE category_id='$id'";
    }else if ($type=='playlist_id' && $id != '') {
    	$params="WHERE playlist_id='$id'";
    }else if ($type=="search") {
    	$params="WHERE video_title LIKE '%$keyword%'";
    }else if($type=='video_id' && $id !=''){
    	$params="WHERE videos.video_id='$id'";
    }else if($type=='user_id' && $id !=''){
    	$params="WHERE videos.user_id='$id'";
    }

    if ($black_list_name!='' && $black_list_value!='') {
    	if ($params == '') {
    		$params="WHERE videos.$black_list_name!='$black_list_value'";
    	}else{
			$params.=" AND videos.$black_list_name!='$black_list_value'";
    	}
    }

    if (!$is_unlimited) {
    	$sql = "SELECT video_id FROM videos ".$params;
		$rl = mysqli_query($conn, $sql);
		$res['totalVideo'] = mysqli_num_rows($rl);
    	$res['limit'] = $limit;
	    $res['page'] = $page;
	    $res['totalPage'] = ceil($res['totalVideo'] / $res['limit']);
	    $start = ($res['page'] - 1) * $res['limit'];
	    $sql_limit="LIMIT $start, $limit";
    }

	$sql2 = "SELECT * FROM videos INNER JOIN users ON videos.user_id = users.user_id $params ORDER BY $order_by $order_type $sql_limit";
	$rl2 = mysqli_query($conn, $sql2);
	
	while ( $row = mysqli_fetch_assoc($rl2) ) {
		$video_id = $row['video_id'];
		$user_id = $row['user_id'];

		$sql3 = "SELECT COUNT(vote_id) as count_like FROM video_votes WHERE vote_type='0' AND video_id='$video_id'";
		$rl3 = mysqli_query($conn, $sql3);
		$like = mysqli_fetch_assoc($rl3);

		$sql4 = "SELECT COUNT(vote_id) as count_dislike FROM video_votes WHERE vote_type='1' AND video_id='$video_id'";
		$rl4 = mysqli_query($conn, $sql4);
		$dislike = mysqli_fetch_assoc($rl4);

		$sql5 = "SELECT COUNT(subscribe_id) as total_subscribe FROM subscriptions WHERE subscribed_to_id='$user_id'";
		$rl5 = mysqli_query($conn, $sql5);
		$subscribe = mysqli_fetch_assoc($rl5);

		$sql6 = "SELECT COUNT(cmt_id) as count_cmt FROM comments WHERE cmt_parent_id='0' AND video_id='$video_id'";
		$rl6 = mysqli_query($conn, $sql6);
		$cmt = mysqli_fetch_assoc($rl6);

		
		$user_name = $row['user_name'];
		$user_tag = $row['user_tag'];
		$user_avatar = $row['user_avatar'];
		$user_tag = $row['user_tag'];
		$user_total_subscribe = $subscribe['total_subscribe'];

		$category_id = $row['category_id'];
		$playlist_id = $row['playlist_id'];
		$video_title = $row['video_title'];
		$video_link = $row['video_link'];
		$video_public_id = $row['video_public_id'];
		$video_poster = $row['video_poster'];
		$video_views = $row['video_views'];
		$video_des = $row['video_des'];
		$video_like = $like['count_like'];
		$video_dislike = $dislike['count_dislike'];
		$video_cmt = $cmt['count_cmt'];
		$video_duration = $row['video_duration'];
		$playlist_update_time = $row['playlist_update_time'];
		$video_created_at = $row['video_created_at'];
		$video_updated_at = $row['video_updated_at'];

		array_push($res['videoList'], [
			'user_id' => $user_id,
			'user_name' => $user_name,
			'user_tag' => $user_tag,
			'user_avatar' => $user_avatar ? URLImgUser().$user_avatar : '',
			'user_tag' => $user_tag,
			'user_total_subscribe' => $user_total_subscribe,
			'category_id' => $category_id,
			'playlist_id' => $playlist_id,
			'video_id' => $video_id,
			'video_public_id' => $video_public_id,
			'video_title' => $video_title,
			'video_link' => $video_link,
			'video_poster' => URLImgVideo().$video_poster,
			'video_views' => $video_views,
			'video_des' => $video_des,
			'video_like' => $video_like,
			'video_dislike' => $video_dislike,
			'video_cmt' => $video_cmt,
			'video_duration' => $video_duration,
			'playlist_update_time' => $playlist_update_time,
			'video_created_at' => $video_created_at,
			'video_updated_at' => $video_updated_at,
		]);
	}
	echo json_encode($res);
?>