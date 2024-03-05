<?php 
include('../connect.php');
include('../jwt.php');
include('../library.php');

    $res = [];
    
    $video_id = $_POST['_video_id'];
    $host_user_id=$_POST['_host_user_id'];
    $parent_id = $_POST['_parent_id'];
    $content = $_POST['_content'];
    $timestamp = getTimeCurrent();
    $time=time();
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

    if ($video_id == '' || $parent_id == '' || $content=='') {
        array_push($res, ['error'=> true, 'message'=> 'Bạn chưa nhập đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $sql = "INSERT INTO comments(video_id, user_id, host_user_id, cmt_parent_id, cmt_content)
            VALUES('$video_id', '$user_id', '$host_user_id', '$parent_id', '$content')";
    $rl = mysqli_query($conn, $sql);

    $idIsert = mysqli_insert_id($conn);

    if ($idIsert > 0) {
        $data=[
            "cmt_id" => $idIsert,
            "video_id" => $video_id,
            "user_id" => $user_id,
            "host_user_id" => $host_user_id,
            "cmt_parent_id" => $parent_id,
            "cmt_content" => $content,
            "cmt_heart" => false,
            "cmt_edited" => false,
            "cmt_created_at" => $timestamp,
            "cmt_updated_at" => $timestamp,
            'cmt_time'=>$time,
            'count_reply' => 0,
            'count_like' => 0,
            'count_dislike' => 0,

        ];
        array_push($res, ['error'=> false,'message'=> 'Thêm bình luận thành công !','data'=>$data]);
        echo json_encode($res);
        die();
    }else{
        array_push($res, ['error'=> true,'message'=> 'Thêm bình luận thất bại !']);
        echo json_encode($res);
    }
?>