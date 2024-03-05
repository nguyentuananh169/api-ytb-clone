<?php 
    include('../connect.php');
    include('../jwt.php');
    include('../library.php');
    $res=[];
    $headers = apache_request_headers();
    $token = $headers['access_token'];
    $token = str_replace('Bearer ', '', $token);
    $verify = verifyAccessToken($token);
    if ($verify['err']) {
        array_push($res, ['error'=>true, 'message'=>$verify['msg']]);
        echo json_encode($res);
        die();
    }
    $userId = $verify['user']['user_id'];
    $sql = "SELECT * FROM users WHERE user_id='$userId'";
    $rl = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($rl);
    if($num == 0){
        array_push($res, ['error'=>true, 'message'=>'Tài khoản không tồn tại']);
        echo json_encode($res);
    }else{
        $data = mysqli_fetch_assoc($rl);
        array_push($res, [
            'error' => false,
            'message'=>'ok',
            'user' => [
                'user_id'=>$data['user_id'],
                'user_name'=>$data['user_name'],
                'user_email'=>$data['user_email'],
                'user_tag'=>$data['user_tag'],
                'user_avatar'=>$data['user_avatar'] ? URLImgUser().$data['user_avatar'] : '',
                'user_des'=>$data['user_des'] ,
                'user_banner'=>$data['user_banner'] ? URLImgUserBanner().$data['user_banner'] : '' ,
            ],
            ]);
        echo json_encode($res);
    }
?>