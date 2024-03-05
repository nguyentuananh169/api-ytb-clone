<?php 
include('../connect.php');
include('../jwt.php');
    $res = [];
    $id = $_GET['_id'] ;

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
    if ($id == '' ) {
        array_push($res, ['error'=> true,'message'=> 'Chưa có đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $sqlCheck = "SELECT video_id FROM videos WHERE playlist_id='$id'";
    $rlCheck = mysqli_query($conn, $sqlCheck);
    $check = mysqli_num_rows($rlCheck);

    if ($check > 0) {
        array_push($res, ['error'=> true,'message'=> 'Danh sách này còn video , không thể xóa !']);
        echo json_encode($res);
        die();
    }

    $sql = "DELETE FROM playlists WHERE playlist_id='$id'";
    $rl = mysqli_query($conn, $sql);

    $sqlSelect2 = "SELECT playlist_id FROM playlists WHERE playlist_id='$id'";
    $rlSelect2 = mysqli_query($conn, $sqlSelect2);
    $num2 = mysqli_num_rows($rlSelect2);

    if ($num2 > 0) {
        array_push($res, ['error'=> true,'message'=> 'Xóa thất bại !']);
        echo json_encode($res);
    }else{
        array_push($res, ['error'=> false,'message'=> 'Xóa thành công !']);
        echo json_encode($res);
    }
?>