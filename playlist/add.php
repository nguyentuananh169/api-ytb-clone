<?php 
include('../connect.php');
include('../jwt.php');

    $res = [];
    
    $title = $_POST['_title'];
    $des = $_POST['_des'];
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

    if ($title == '' ) {
        array_push($res, ['error'=> true, 'message'=> 'Bạn chưa nhập đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $sqlCheck = "SELECT playlist_name FROM playlists WHERE playlist_name='$title'";
    $rlCheck = mysqli_query($conn,$sqlCheck);
    $check = mysqli_num_rows($rlCheck);
    if ($check > 0) {
        array_push($res, ['error'=>true, 'message'=>'Tên danh sách phát đã tồn tại']);
        echo json_encode($res);
        die();
    }

    $sql = "INSERT INTO playlists(user_id, playlist_name, playlist_des)
            VALUES('$user_id', '$title', '$des')";
    $rl = mysqli_query($conn, $sql);

    $idIsert = mysqli_insert_id($conn);

    if ($idIsert > 0) {
        array_push($res, ['error'=> false,'message'=> 'Thêm video thành công !']);
        echo json_encode($res);
        die();
    }else{
        array_push($res, ['error'=> true,'message'=> 'Thêm video thất bại !']);
        echo json_encode($res);
    }
?>