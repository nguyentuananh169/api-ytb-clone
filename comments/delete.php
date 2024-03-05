<?php 
include('../connect.php');
include('../jwt.php');
    $res = [];
    $cmt_id = $_GET['_cmt_id'];
    $parent_id = $_GET['_cmt_parent_id'];
    $params = '';
    $params = '';
    if ($parent_id > 0) {
        $params="WHERE cmt_id='$cmt_id'";
    }else{
        $params="WHERE cmt_id='$cmt_id' OR cmt_parent_id='$cmt_id'";
    }

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
    if ($cmt_id == '' || $parent_id == '' ) {
        array_push($res, ['error'=> true,'message'=> 'Chưa có đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $sql = "DELETE FROM comments $params";
    $rl = mysqli_query($conn, $sql);

    $sqlCheck = "SELECT cmt_id FROM comments WHERE cmt_id='$cmt_id'";
    $rlCheck = mysqli_query($conn, $sqlCheck);
    $check = mysqli_num_rows($rlCheck);

    if ($check > 0) {
        array_push($res, ['error'=> true,'message'=> 'Xóa thất bại !']);
        echo json_encode($res);
    }else{
        $sql2 = "DELETE FROM comment_vote $params";
        $rl2 = mysqli_query($conn, $sql2);

        array_push($res, ['error'=> false,'message'=> 'Xóa thành công !']);
        echo json_encode($res);
    }
?>