<?php 
	include('../connect.php');
	include('../jwt.php');

		$res = [];
		$video_id = $_POST['_video_id'];
	    
	    if ($video_id == '') {
	    	array_push($res, ['error'=> true,'message'=> 'Chưa đủ thông tin']);
        	echo json_encode($res);
	    	die();
	    }
	    $sql = "SELECT video_views FROM videos WHERE video_id='$video_id'";
	    $rl = mysqli_query($conn,$sql);
	    $data=mysqli_fetch_assoc($rl);
	    $view=$data['video_views']+1;

    	$sqlUpdate = "UPDATE videos SET video_views='$view' WHERE video_id='$video_id'";
    	$rlUpdate = mysqli_query($conn, $sqlUpdate);

    	array_push($res, ['error'=> false,'message'=> 'Cập nhật thành công!']);
    	echo json_encode($res);
	    
?>