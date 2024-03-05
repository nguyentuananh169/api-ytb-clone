<?php 
	include('../connect.php');
	include('../jwt.php');

		$res = [];
		$id = $_POST['_id'];
		$name = $_POST['_name'];
		$des = $_POST['_des'];

	    $headers = apache_request_headers();
	    $token = $headers['access_token'];
	    $token = str_replace('Bearer ', '', $token);
	    $verify = verifyAccessToken($token);
	    $time = time();

	    if ($verify['err']) {
	        array_push($res, ['error'=>true, 'message'=>$verify['msg']]);
	        echo json_encode($res);
	        die();
	    }

	    if ($id == '' || $name == '') {
	    	array_push($res, ['error'=> true,'message'=> 'Bạn chưa nhập đủ thông tin']);
        	echo json_encode($res);
	    	die();
	    }

    	$sqlUpdate = "UPDATE playlists SET playlist_name='$name', playlist_des='$des' WHERE playlist_id='$id'";
    	$rlUpdate = mysqli_query($conn, $sqlUpdate);
    	
    	array_push($res, ['error'=> false,'message'=> 'Sửa thành công !']);
    	echo json_encode($res);
    
?>