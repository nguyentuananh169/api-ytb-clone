<?php 
	include('../connect.php');
	include('../jwt.php');
	include('../library.php');
		$res = [];
		$id = $_POST['_id'];
		$title = $_POST['_title'];
		$des = $_POST['_des'];
		$poster = $_FILES['_poster'];
		$playlist_id = $_POST['_playlist_id'];
		$category_id = $_POST['_category_id'];
	    $headers = apache_request_headers();
	    $token = $headers['access_token'];
	    $token = str_replace('Bearer ', '', $token);
	    $verify = verifyAccessToken($token);
	    $time = time();
	    $current_playlist_update_time="";

	    if ($verify['err']) {
	        array_push($res, ['error'=>true, 'message'=>$verify['msg']]);
	        echo json_encode($res);
	        die();
	    }
	    
	    if ($id == '' || $title == '' || $category_id == '') {
	    	array_push($res, ['error'=> true,'message'=> 'Bạn chưa nhập đủ thông tin']);
        	echo json_encode($res);
	    	die();
	    }
	    if ($playlist_id != '' || $poster['name'] != '') {
	    	$sqlSelect = "SELECT playlist_id, video_poster, playlist_update_time FROM videos WHERE video_id = '$id'";
	    	$rlSelect = mysqli_query($conn, $sqlSelect);
	    	$data = mysqli_fetch_assoc($rlSelect);

	    	$current_playlist_update_time = $data['playlist_id'] != $playlist_id ? getTimeCurrent() : $data['playlist_update_time'];
	    }

	    if ($poster['name'] == '') {
	    	$sqlUpdate = "UPDATE videos SET video_title='$title', video_des='$des', playlist_id='$playlist_id', category_id='$category_id',playlist_update_time='$current_playlist_update_time' WHERE video_id='$id'";
	    	$rlUpdate = mysqli_query($conn, $sqlUpdate);
	    	
	    	array_push($res, ['error'=> false,'message'=> 'Sửa thành công !']);
        	echo json_encode($res);
	    }else{
	    	if ($poster['type'] != 'image/png' && $poster['type'] != 'image/jpeg' && $poster['type'] != 'image/gif') {
	    		array_push($res, ['error'=> true,'message'=> 'File bạn nhập không đúng định dạng (PNG, JPEG, GIF) !']);
        		echo json_encode($res);
		    	die();
		    }

	    	unlink('../images/video/'.$data['video_poster']);

	    	move_uploaded_file($poster['tmp_name'], '../images/video/'.$time.$poster['name']);

		    $sqlUpdate = "UPDATE videos SET video_poster='".$time.$poster['name']."', video_title='$title', video_des='$des', playlist_id='$playlist_id', category_id='$category_id',playlist_update_time='$current_playlist_update_time' WHERE video_id='$id'";
	    	$rlUpdate = mysqli_query($conn, $sqlUpdate);

	    	array_push($res, ['error'=> false,'message'=> 'Sửa thành công!']);
        	echo json_encode($res);
	    }
?>