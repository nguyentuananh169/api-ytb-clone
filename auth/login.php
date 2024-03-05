<?php 
    include('../connect.php');
    include('../jwt.php');
    include('../library.php');
    $res = [];

    $email = $_POST['_email'];
    $password = $_POST['_password'];

    if($email == '' || $password == ''){
        array_push($res, ['error'=> true, 'message'=> 'Bạn chưa nhập đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $passwordMd5 = md5($password);

    $sqlCheck = "SELECT * FROM users WHERE user_email = '$email' AND user_password = '$passwordMd5'";
    $rlCheck = mysqli_query($conn, $sqlCheck);
    $check = mysqli_num_rows($rlCheck);

    if($check == 0){
        array_push($res, ['error'=> true, 'message'=> 'Email hoặc mật khẩu bạn nhập không đúng']);
        echo json_encode($res);
        die();
    }

    $data = mysqli_fetch_assoc($rlCheck);

    array_push($res, [
        'error' => false,
        'message'=>'Đăng nhập thành công',
        'access_token' => createAccessToken(['user_id'=> $data['user_id']]),
        'refresh_token' => createRefreshToken(['user_id'=> $data['user_id']]),
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
?>