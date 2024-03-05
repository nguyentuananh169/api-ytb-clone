<?php 
include('../connect.php');
include('../jwt.php');

    $res = [];
    $cmt_id = $_POST['_cmt_id'];
    $type = $_POST['_type'];

    if ($cmt_id == '') {
        array_push($res, ['error'=> true, 'message'=> 'Bạn chưa nhập đủ thông tin']);
        echo json_encode($res);
        die();
    }

    if ($type == 'pin') {
        $sql = "UPDATE comments SET cmt_pin='1' WHERE cmt_id='$cmt_id'";
        $rl = mysqli_query($conn, $sql);
    }else if ($type == 'unpin') {
        $sql = "UPDATE comments SET cmt_pin='0' WHERE cmt_id='$cmt_id'";
        $rl = mysqli_query($conn, $sql);
    }
    array_push($res, ['error'=> false,'message'=> 'ok !']);
    echo json_encode($res);
?>