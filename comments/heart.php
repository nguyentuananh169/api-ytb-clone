<?php 
include('../connect.php');
include('../jwt.php');

    $res = [];
    
    $cmt_id = $_POST['_cmt_id'];
    $value = $_POST['_value'];
    
    if ($cmt_id == '' || $value == '') {
        array_push($res, ['error'=> true, 'message'=> 'Bạn chưa nhập đủ thông tin']);
        echo json_encode($res);
        die();
    }

    $sql = "UPDATE comments SET cmt_heart='$value' WHERE cmt_id='$cmt_id'";
    $rl = mysqli_query($conn, $sql);

    array_push($res, ['error'=> false,'message'=> 'ok', 'is_heart'=> $value == 0 ? false: true]);
    echo json_encode($res);
?>