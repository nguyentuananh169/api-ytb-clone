<?php
	include('../connect.php');
	include('../jwt.php');

	$res = [];
	$post_id = $_GET['_post_id'] ? $_GET['_post_id'] : '';
	$post_img = $_GET['_post_img'] ? $_GET['_post_img'] : '';

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

	$sqlDelete = "DELETE FROM posts WHERE post_id='$post_id' AND user_id='$user_id'";
	$rlDelete = mysqli_query($conn,$sqlDelete);
	
	$sqlCheck = "SELECT post_id FROM posts WHERE post_id='$post_id' AND user_id='$user_id'";
	$rlCheck = mysqli_query($conn,$sqlCheck);
	$check = mysqli_num_rows();

	if ($check > 0) {
		array_push($res, ['error'=>true, 'message'=>'Xóa thất bại']);
        echo json_encode($res);
        die();
	}

	$imgArr = json_decode($post_img);
	if (count($imgArr) > 0) {
		for ($i=0; $i < count($imgArr) ; $i++) { 
            unlink('../images/posts/'.$imgArr[$i]);
        }
	}

	$sql2 = "DELETE FROM post_comments WHERE post_id='$post_id'";
    $rl2 = mysqli_query($conn, $sql2);

    $sql3 = "DELETE FROM post_comment_vote WHERE post_id='$post_id'";
    $rl3 = mysqli_query($conn, $sql3);

    $sql4 = "DELETE FROM post_votes WHERE post_id='$post_id'";
    $rl4 = mysqli_query($conn, $sql4);

	array_push($res, ['error'=>false, 'message'=>'Xóa thành công']);
    echo json_encode($res);
?>