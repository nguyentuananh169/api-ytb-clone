<?php 
	include('../connect.php');
	include('../library.php');
	include('../jwt.php');
	
	$res = [
		'totalPosts' => '',
        'totalPage' => '',
        'page' => '',
        'limit' => '',
		'postsList'=> [],
	];
	$params = '';
	$user_id = $_GET['_user_id'] ? $_GET['_user_id'] : '';
	$id = $_GET['_id'] ? $_GET['_id'] : '';
	$video_id = $_GET['_video_id'];
	$post_type = $_GET['_post_type'] ? $_GET['_post_type'] : '';
	$type = $_GET['_type'] ? $_GET['_type'] : '';
    $limit = $_GET['_limit'] ? (int)$_GET['_limit'] : 10;
    $page = $_GET['_page'] ? (int)$_GET['_page']: 1;
    $order_by = $_GET['_order_by'] ? $_GET['_order_by'] : 'post_id';
    $order_type = $_GET['_order_type'] ? $_GET['_order_type'] : 'DESC';
    $count = $_GET['_count'] ? $_GET['_count'] : 0;

    $headers = apache_request_headers();
    $token = $headers['access_token'];
    $token = str_replace('Bearer ', '', $token);
    $verify = verifyAccessToken($token);

    if (!$verify['err']) {
        $token_user_id = $verify['user']['user_id'];
    }

    if ($type == 'get_by_token') {
	    if ($verify['err']) {
	        array_push($res, ['error'=>true, 'message'=>$verify['msg']]);
	        echo json_encode($res);
	        die();
	    }
	    $user_id = $verify['user']['user_id'];
	    $params = "WHERE posts.user_id='$user_id'";
    }else if ($type=='get_by_user' && $user_id !== '') {
    	$params = "WHERE posts.user_id='$user_id'";
    }else if ($type=='get_by_id' && $id !== '') {
    	$params = "WHERE posts.post_id='$id'";
    }
    
	$sql = "SELECT post_id FROM posts $params";
	$rl = mysqli_query($conn, $sql);
	$res['totalPosts'] = mysqli_num_rows($rl);
	$res['limit'] = $limit;
    $res['page'] = $page;
    $res['totalPage'] = ceil($res['totalPosts'] / $res['limit']);
    $start = ($res['page'] - 1) * $res['limit'];
    if ($count > 0) {
    	$start = $start - $count;
    	$limit = $limit + $count;
    }
    $sql_limit="LIMIT $start, $limit"; 

	$sql2 = "SELECT * FROM posts INNER JOIN users ON posts.user_id = users.user_id $params ORDER BY $order_by $order_type $sql_limit";
	$rl2 = mysqli_query($conn, $sql2);
	
	while ( $row = mysqli_fetch_assoc($rl2) ) {
		$post_type = $row['post_type'];
		$video_id = $row['video_id'];
		$user_name = $row['user_name'];
		$user_avatar = $row['user_avatar'];
		$video_data = '';
		if ($post_type == 'video_id') {
			$sql3 = "SELECT * FROM videos WHERE videos.video_id='$video_id'";
			$rl3 = mysqli_query($conn, $sql3);
			$data = mysqli_fetch_assoc($rl3);
			$video_data = [
				'category_id'=>$data['category_id'],
				'playlist_id'=>$data['playlist_id'],
				'playlist_update_time' => $data['playlist_update_time'],
				'user_id'=>$data['user_id'],
				'user_name'=>$user_name,
				'user_avatar'=>$user_avatar ? URLImgUser().$user_avatar : '',
				'video_created_at'=>$data['video_created_at'],
				'video_des'=>$data['video_des'],
				'video_duration'=>$data['video_duration'],
				'video_id'=>$data['video_id'],
				'video_type'=>$data['video_type'],
				'video_link'=>$data['video_link'],
				'video_poster'=>URLImgVideo().$data['video_poster'],
				'video_title'=>$data['video_title'],
				'video_updated_at'=>$data['video_updated_at'],
				'video_views'=>$data['video_views'],
			];
		}
		$post_id = $row['post_id'];

		$sql3 = "SELECT COUNT(vote_id) as count_like FROM post_votes WHERE vote_type='0' AND post_id='$post_id'";
		$rl3 = mysqli_query($conn, $sql3);
		$like = mysqli_fetch_assoc($rl3);

		$sql4 = "SELECT COUNT(vote_id) as count_dislike FROM post_votes WHERE vote_type='1' AND post_id='$post_id'";
		$rl4 = mysqli_query($conn, $sql4);
		$dislike = mysqli_fetch_assoc($rl4);

		$sql5 = "SELECT COUNT(cmt_id) as total_cmt FROM post_comments WHERE cmt_parent_id='0' AND post_id='$post_id'";
		$rl5 = mysqli_query($conn, $sql5);
		$cmt = mysqli_fetch_assoc($rl5);

		if (isset($token_user_id)) {
			$vote_type="";
			$sqlCheckVote = "SELECT vote_type FROM post_votes WHERE user_id='$token_user_id' AND post_id='$post_id'";
		    $rlCheckVote = mysqli_query($conn, $sqlCheckVote);
		    $data = mysqli_fetch_assoc($rlCheckVote);
		    $check = mysqli_num_rows($rlCheckVote);
		    if ($check > 0) {
		    	$vote_type = $data['vote_type'] > 0 ? 'dislike' :'like';
		    }
		}
		
		$video_id = $row['video_id'];
		$user_id = $row['user_id'];

		$post_type = $row['post_type'];
		$post_content = $row['post_content'];
		$post_img = $row['post_img'];
		$post_created_at = $row['post_created_at'];
		$post_updated_at = $row['post_updated_at'];

		array_push($res['postsList'], [
			'user_id' => $user_id,
			'post_id' => $post_id,
			'video_id' => $video_id,
			'post_type' => $post_type,
			'post_content' => $post_content,
			'post_img' => $post_img,
			'base_url_img' => URLImgPost(),
			'post_created_at' => $post_created_at,
			'post_updated_at' => $post_updated_at,
			'user_name' => $user_name,
			'user_avatar' => $user_avatar ? URLImgUser().$user_avatar : '',
			'count_like'=>$like['count_like'],
			'count_dislike'=>$dislike['count_dislike'],
			'count_cmt'=>$cmt['total_cmt'],
			'vote_type' => $vote_type,
			'video_data' => $video_data,
		]);
	}
	echo json_encode($res);
?>