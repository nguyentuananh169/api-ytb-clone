<?php 
include('../connect.php');
include('../jwt.php');
    $res = [];
    $video_id = $_GET['_video_id'];

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
    $sql = "SELECT user_id FROM users WHERE user_id='$user_id'";
    $rl = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($rl);
    if($num <= 0){
        array_push($res, ['error'=>true, 'message'=>'Tài khoản không tồn tại']);
        echo json_encode($res);
        die();
    }

    if ($video_id == '') {
         array_push($res, ['error'=>true, 'message'=>'Chưa đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $sql = "DELETE FROM video_votes WHERE video_id='$video_id' AND user_id='$user_id'";
    $rl = mysqli_query($conn, $sql);
    
    $sqlCheck = "SELECT vote_id FROM video_votes WHERE video_id='$video_id' AND user_id='$user_id'";
    $rlCheck = mysqli_query($conn, $sqlCheck);

    $check = mysqli_num_rows($rlCheck);

    if ($check > 0) {
        array_push($res, ['error'=> true,'message'=> 'Hủy thất bại !']);
        echo json_encode($res);
    }else{
        array_push($res, ['error'=> false,'message'=> 'Hủy thành công !']);
        echo json_encode($res);
    }
?>