<?php 
include('../connect.php');
include('../jwt.php');
include('../library.php');
include('../cloudinary/index.php');

    $res = [];
    
    $category = $_POST['_category'];
    $playlist = $_POST['_playlist'];
    $video_file = $_FILES['_video_file'];
    $poster = $_FILES['_poster'];
    $title = $_POST['_title'];
    $des = $_POST['_des'];
    $time = time();
    $playlist_update_time = $playlist ? getTimeCurrent() : '';
    $views = rand(500, 20000000);
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

    if ($category == '' || $title == '' || $poster['name'] == ''|| $video_file['name'] == '') {
        array_push($res, ['error'=> true, 'message'=> 'Bạn chưa nhập đủ thông tin']);
        echo json_encode($res);
        die();
    }

    if ($video_file['type'] != 'video/mp4') {
        array_push($res, ['error'=> true, 'message'=> 'Video không đúng định dạng (MP4)']);
        echo json_encode($res);
        die();
    }

    if ($poster['type'] != 'image/png' && $poster['type'] != 'image/jpeg' && $poster['type'] != 'image/gif') {
        array_push($res, ['error'=> true, 'message'=> 'Hình ảnh đại diện bạn nhập không đúng định dạng (PNG, JPEG, GIF)']);
        echo json_encode($res);
        die();
    }

    $video_url = $video_file['tmp_name'];
    $data = cloudinary_upload($video_url);
    if($data['error']){
        array_push($res, ['error'=> true,'message'=> $data['message']]);
        echo json_encode($res);
        die();
    }
    $video_link = $data['data']['url'];
    $video_public_id = $data['data']['public_id'];
    $video_duration = $data['data']['duration'];

    $sql = "INSERT INTO videos(user_id, category_id, playlist_id, video_title, video_public_id, video_link, video_poster, video_des, video_duration, playlist_update_time,video_views)
            VALUES('$user_id', '$category', '$playlist', '$title', '$video_public_id', '$video_link', '".$time.$poster['name']."', '$des','$video_duration','$playlist_update_time','$views')";
    $rl = mysqli_query($conn, $sql);

    $idIsert = mysqli_insert_id($conn);

    if ($idIsert > 0) {
        move_uploaded_file($poster['tmp_name'], '../images/video/'.$time.$poster['name']);
        array_push($res, ['error'=> false,'message'=> 'Thêm video thành công !']);
        echo json_encode($res);
        die();
    }else{
        array_push($res, ['error'=> true,'message'=> 'Thêm video thất bại !']);
        echo json_encode($res);
    }
?>