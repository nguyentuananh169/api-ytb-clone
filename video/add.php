<?php 
include('../connect.php');
include('../jwt.php');
include('../library.php');
include('../cloudinary/index.php');

    $res = [];
    
    $category = $_POST['_category'] ? $_POST['_category'] : 0;
    $playlist = $_POST['_playlist'];
    $video_type = $_POST['_video_type'] ? $_POST['_video_type'] : '0';
    // $video_file = $_FILES['_video_file'];
    $video_link = $_POST['_video_link'];
    $video_public_id = $_POST['_video_public_id'];
    $video_duration =  $_POST['_video_duration'];
    $poster = $_FILES['_poster'];
    $poster_link = $_POST['_poster_link'];
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

    $poster_name = $poster['name'];
    if ($video_type > 0) {
        $poster_name = 'image.jpg';
    }

    if ($title == '' || $poster_name == ''|| $video_link == '') {
        array_push($res, ['error'=> true, 'message'=> 'Bạn có đủ thông tin']);
        echo json_encode($res);
        die();
    }

    // if ($video_file['type'] != 'video/mp4') {
    //     array_push($res, ['error'=> true, 'message'=> 'Video không đúng định dạng (MP4)','test'=>$video_file]);
    //     echo json_encode($res);
    //     die();
    // }

    if ($poster['type'] != 'image/png' && $poster['type'] != 'image/jpeg' && $poster['type'] != 'image/gif' && $video_type == '0') {
        array_push($res, ['error'=> true, 'message'=> 'Hình ảnh đại diện bạn nhập không đúng định dạng (PNG, JPEG, GIF)']);
        echo json_encode($res);
        die();
    }

    // $video_url = $video_file['tmp_name'];
    // $data = cloudinary_upload($video_url);
    // if($data['error']){
    //     array_push($res, ['error'=> true,'message'=> $data['message']]);
    //     echo json_encode($res);
    //     die();
    // }
    // $video_link = $data['data']['url'];
    // $video_public_id = $data['data']['public_id'];
    // $video_duration = $data['data']['duration'];

    $sql = "INSERT INTO videos(user_id, category_id, playlist_id, video_type, video_title, video_public_id, video_link, video_poster, video_des, video_duration, playlist_update_time,video_views)
            VALUES('$user_id', '$category', '$playlist', '$video_type', '$title', '$video_public_id', '$video_link', '".$time.$poster_name."', '$des','$video_duration','$playlist_update_time','$views')";
    $rl = mysqli_query($conn, $sql);

    $idIsert = mysqli_insert_id($conn);

    if ($idIsert > 0) {
        if ($video_type > 0) {
            $base64_data = str_replace('data:image/jpeg;base64,', '', $poster_link);
            $base64_data = str_replace(' ', '+', $base64_data);
            $decoded_data = base64_decode($base64_data);
            $url = '../images/video/'.$time.$poster_name;
            file_put_contents($url, $decoded_data);
        }else{
            move_uploaded_file($poster['tmp_name'], '../images/video/'.$time.$poster_name);
        }
        $type = $video_type > 0 ? "ngắn " : '';
        array_push($res, ['error'=> false,'message'=> 'Thêm video '.$type.'thành công !']);
        echo json_encode($res);
        die();
    }else{
        array_push($res, ['error'=> true,'message'=> 'Thêm video '.$type.'thất bại !']);
        echo json_encode($res);
    }
?>