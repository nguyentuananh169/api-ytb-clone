<?php 
include('../connect.php');
include('../jwt.php');

    $res = [];
    
    $video_id = $_POST['_video_id'];
    $vote_type = $_POST['_vote_type'];

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
    if($num == 0){
        array_push($res, ['error'=>true, 'message'=>'Tài khoản không tồn tại']);
        echo json_encode($res);
        die();
    }

    if ($video_id == '' || $vote_type == '') {
        array_push($res, ['error'=> true, 'message'=> 'Chưa đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $sql = "UPDATE video_votes SET vote_type='$vote_type' WHERE video_id='$video_id' AND user_id='$user_id'";
    $rl = mysqli_query($conn,$sql);
    
    array_push($res, ['error'=> false,'message'=> 'ok','vote_type'=>$vote_type > 0 ? 'dislike' : 'like',]);
    echo json_encode($res);
    
?>