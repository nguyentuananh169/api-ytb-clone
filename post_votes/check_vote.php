<?php 
    include('../connect.php');
    include('../jwt.php');

    $res = [];
    
    $post_id = $_GET['_post_id'];

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

    if ($post_id == '') {
        array_push($res, ['error'=> true, 'message'=> 'Chưa đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $sql = "SELECT vote_type FROM post_votes WHERE user_id='$user_id' AND post_id='$post_id'";
    $rl = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($rl);
    $check = mysqli_num_rows($rl);

    if ($check > 0) {
        array_push($res, ['vote_type'=> $data['vote_type'] > 0 ? 'dislike' :'like','message'=> 'ok']);
        echo json_encode($res);
    }
    else{
        array_push($res, ['vote_type'=> '','message'=> 'Chưa có dữ liệu']);
        echo json_encode($res);
    }
?>