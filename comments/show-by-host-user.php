<?php 
	include('../connect.php');
	include('../library.php');
	include('../jwt.php');
	
	$res = [
		'totalComment' => '',
        'totalPage' => '',
        'page' => '',
        'limit' => '',
		'commentList'=> []
	];

	$get_video_id = $_GET['_get_video_id'] ? $_GET['_get_video_id']: "";
	$parent_id = $_GET['_parent_id'] ? $_GET['_parent_id'] : '0';
    $limit = $_GET['_limit'] ? (int)$_GET['_limit'] : 10;
    $page = $_GET['_page'] ? (int)$_GET['_page']: 1;
    $order_by = $_GET['_order_by'] ? $_GET['_order_by'] : 'cmt_id';
    $order_type = $_GET['_order_type'] ? $_GET['_order_type'] : 'DESC';
    $count = $_GET['_count'] ? $_GET['_count'] : 0;
    $params = "";

    $headers = apache_request_headers();
    $token = $headers['access_token'];
    $token = str_replace('Bearer ', '', $token);
    $verify = verifyAccessToken($token);
    if ($verify['err']) {
        array_push($res, );
        echo json_encode(['error'=>true, 'message'=>$verify['msg']]);
        die();
    }
    $host_user_id = $verify['user']['user_id'];

    if ($get_video_id != '') {
    	$params = "AND comments.video_id='$get_video_id'";
    }

	$sql = "SELECT cmt_id FROM comments WHERE cmt_parent_id='$parent_id' AND host_user_id='$host_user_id' $params";
	$rl = mysqli_query($conn, $sql);
	$res['totalComment'] = mysqli_num_rows($rl);
	$res['limit'] = $limit;
    $res['page'] = $page;
    $res['totalPage'] = ceil($res['totalComment'] / $res['limit']);
    $start = ($res['page'] - 1) * $res['limit'];
    if ($count > 0) {
    	$start = $start - $count;
    	$limit = $limit + $count;
    }
    $sql_limit="LIMIT $start, $limit"; 

	$sql2 = "SELECT * FROM comments INNER JOIN users ON comments.user_id = users.user_id WHERE comments.cmt_parent_id='$parent_id' AND host_user_id='$host_user_id' $params ORDER BY $order_by $order_type $sql_limit";
	$rl2 = mysqli_query($conn, $sql2);
	
	while ( $row = mysqli_fetch_assoc($rl2) ) {
		$cmt_id = $row['cmt_id'];
		$user_id = $row['user_id'];
		$video_id = $row['video_id'];
		$category_id = "";
		$video_poster = '';
		$video_title = "";

		if ($parent_id === '0') {
			$sqlReply = "SELECT COUNT(cmt_id) as count_reply FROM comments WHERE cmt_parent_id='$cmt_id'";
			$rlReply = mysqli_query($conn,$sqlReply); 
			$reply = mysqli_fetch_assoc($rlReply);

			$sqlVideo = "SELECT video_poster, video_title,category_id,video_type FROM videos WHERE video_id='$video_id'";
			$rlVideo = mysqli_query($conn,$sqlVideo); 
			$video_info = mysqli_fetch_assoc($rlVideo);
			$video_poster = $video_info['video_poster'];
			$video_title = $video_info['video_title'];
			$video_type = $video_info['video_type'];
			$category_id = $video_info['category_id'];
		}

		$sqlLike = "SELECT COUNT(vote_id) as count_like FROM comment_vote WHERE vote_type='0' AND video_id='$video_id' AND cmt_id='$cmt_id'";
		$rlLike = mysqli_query($conn, $sqlLike);
		$like = mysqli_fetch_assoc($rlLike);

		$sqlDislike = "SELECT COUNT(vote_id) as count_dislike FROM comment_vote WHERE vote_type='1' AND video_id='$video_id' AND cmt_id='$cmt_id' ";
		$rlDislike = mysqli_query($conn, $sqlDislike);
		$dislike = mysqli_fetch_assoc($rlDislike);

		$vote_type="";
		$sqlCheckVote = "SELECT vote_type FROM comment_vote WHERE user_id='$host_user_id' AND video_id='$video_id' AND cmt_id='$cmt_id'";
	    $rlCheckVote = mysqli_query($conn, $sqlCheckVote);
	    $data = mysqli_fetch_assoc($rlCheckVote);
	    $check = mysqli_num_rows($rlCheckVote);
	    if ($check > 0) {
	    	$vote_type = $data['vote_type'] > 0 ? 'dislike' :'like';
	    }
		
		$user_name = $row['user_name'];
		$user_tag = $row['user_tag'];
		$user_avatar = $row['user_avatar'];

		$cmt_content = $row['cmt_content'];
		$parent = $row['cmt_parent_id'];
		$cmt_heart = $row['cmt_heart'] > 0 ? true : false;
		$cmt_edited = $row['cmt_edited'] > 0 ? true : false;
		$cmt_pin = $row['cmt_pin'] > 0 ? true : false;
		$count_like = $like['count_like'] ;
		$count_dislike = $dislike['count_dislike'] ;
		$vote_type = $vote_type ? $vote_type : '';
		$cmt_created_at = $row['cmt_created_at'];
		$cmt_updated_at = $row['cmt_updated_at'];

		array_push($res['commentList'], [
			'cmt_id' => $cmt_id,
			'video_id' => $video_id,
			'category_id' => $category_id,
			'video_poster' => URLImgVideo().$video_poster,
			'video_title' => $video_title,
			'video_type' => $video_type,
			'user_id' => $user_id,
			'user_name' => $user_name,
			'user_avatar' => $user_avatar ? URLImgUser().$user_avatar : '',
			'user_tag' => $user_tag,
			'cmt_content' => $cmt_content,
			'cmt_heart' => $cmt_heart,
			'cmt_pin' => $cmt_pin,
			'count_like' => $count_like,
			'count_dislike' => $count_dislike,
			'vote_type' => $vote_type,
			'count_reply' => $reply['count_reply'] ? $reply['count_reply'] : 0,
			'cmt_parent_id' => $parent,
			'cmt_created_at' => $cmt_created_at,
			'cmt_updated_at' => $cmt_updated_at,
			'cmt_edited' => $cmt_edited,
			'reply'=>[],

		]);
	}
	echo json_encode($res);
?>