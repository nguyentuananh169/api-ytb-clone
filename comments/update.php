<?php 
	include('../connect.php');
	include('../jwt.php');
	include('../library.php');
		$res = [];
		$video_id = $_POST['_video_id'];
		$cmt_id = $_POST['_cmt_id'];
		$content = $_POST['_content'];
		$timestamp = getTimeCurrent();
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
	    
	    if ($video_id == '' || $cmt_id == '' ) {
	    	array_push($res, ['error'=> true,'message'=> 'Chưa nhập đủ thông tin']);
        	echo json_encode($res);
	    	die();
	    }
	    
    	$sqlUpdate = "UPDATE comments SET cmt_content='$content', cmt_edited='1', cmt_updated_at='$timestamp' WHERE video_id='$video_id' AND cmt_id='$cmt_id' AND user_id='$user_id'";
    	$rlUpdate = mysqli_query($conn, $sqlUpdate);
    	
    	array_push($res, ['error'=> false,'message'=> 'Sửa thành công !','content'=>$content,'timestamp'=>$timestamp,]);
    	echo json_encode($res);
?>