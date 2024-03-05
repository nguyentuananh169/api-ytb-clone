<?php 
    include('../connect.php');
    include('../jwt.php');

    $res = [];
    
    $id = $_GET['_id'];

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

    if ($id == '') {
        array_push($res, ['error'=> true, 'message'=> 'Chưa đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $sql = "SELECT subscribe_id FROM subscriptions WHERE subscriber_id='$user_id' AND subscribed_to_id='$id'";
    $rl = mysqli_query($conn, $sql);

    $check = mysqli_num_rows($rl);

    if ($check > 0) {
        array_push($res, ['is_subscribed'=> true,'message'=> 'Đã đăng ký']);
        echo json_encode($res);
    }else{
        array_push($res, ['is_subscribed'=> false,'message'=> 'Chưa đăng ký']);
        echo json_encode($res);
    }
?>