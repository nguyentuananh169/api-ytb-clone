<?php 
	include('../connect.php');
	include('../library.php');
	include('../jwt.php');
	
	$res = [
		'totalVote' => '',
        'totalPage' => '',
        'page' => '',
        'limit' => '',
		'list'=> [],
	];
	
    $limit = $_GET['_limit'] ? (int)$_GET['_limit'] : 10;
    $page = $_GET['_page'] ? (int)$_GET['_page']: 1;
    $is_unlimited = $_GET['_is_unlimited']? $_GET['_is_unlimited'] : false;

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
    $sql_limit='';
    if (!$is_unlimited) {
    	$sql = "SELECT vote_id FROM video_votes WHERE user_id='$user_id' AND vote_type='0'";
		$rl = mysqli_query($conn, $sql);
		$res['totalVote'] = mysqli_num_rows($rl);
		$res['limit'] = $limit;
	    $res['page'] = $page;
	    $res['totalPage'] = ceil($res['totalVote'] / $res['limit']);
	    $start = ($res['page'] - 1) * $res['limit'];
	    $sql_limit="LIMIT $start, $limit";
    }
	
	$sql2 = "SELECT video_votes.vote_id,video_votes.vote_updated_at,videos.video_id,videos.user_id,videos.video_poster,videos.category_id,videos.video_duration,videos.video_title,videos.video_views,videos.video_link,videos.video_created_at,users.user_avatar,users.user_name FROM video_votes INNER JOIN videos ON video_votes.video_id = videos.video_id INNER JOIN users ON videos.user_id = users.user_id WHERE video_votes.user_id='$user_id' AND video_votes.vote_type='0' ORDER BY video_votes.vote_id DESC $sql_limit";
	$rl2 = mysqli_query($conn, $sql2);
	
	while ( $row = mysqli_fetch_assoc($rl2) ) {
		$user_id2 = $row['user_id'];
		$user_name = $row['user_name'];
		$user_avatar = $row['user_avatar'];
		$vote_id = $row['vote_id'];
		$video_id = $row['video_id'];
		$category_id = $row['category_id'];
		$video_title = $row['video_title'];
		$video_poster = $row['video_poster'];
		$video_views = $row['video_views'];
		$video_link = $row['video_link'];
		$video_duration = $row['video_duration'];
		$video_created_at = $row['video_created_at'];
		$vote_updated_at = $row['vote_updated_at'];

		array_push($res['list'], [
			'vote_id' => $vote_id,
			'user_id' => $user_id2,
			'user_name' => $user_name,
			'user_avatar' => $user_avatar ? URLImgUser().$user_avatar :"",
			'category_id' => $category_id,
			'video_id' => $video_id,
			'video_title' => $video_title,
			'video_poster' => URLImgVideo().$video_poster,
			'video_views' => $video_views,
			'video_link' => $video_link,
			'video_duration' => $video_duration,
			'video_created_at' => $video_created_at,
			'vote_updated_at' => $vote_updated_at,
		]);
	}
	echo json_encode($res);
?>