<?php 
include('../connect.php');
include('../jwt.php');
include('../library.php');
    $res=[];
    $name = $_POST['_name']; 
    $email = $_POST['_email'];
    $password = $_POST['_password'];
    $avatar = $_FILES['_avatar'];
    $time = time();
    

    if($name == '' || $email == '' || $password == ''){
        array_push($res, ['error' => true, 'message' => 'Bạn chưa nhập đủ thông tin!','hl'=>$name.'-'.$email.'-'.$password]);
        echo json_encode($res);
        die();
    }

    $tag = '@'.removeSpecialCharacters($name).'-'.$time;

    $sqlCheckEmail = "SELECT user_email FROM users WHERE user_email = '$email'";
    $rlCheckEmail = mysqli_query($conn, $sqlCheckEmail);
    $checkEmail = mysqli_num_rows($rlCheckEmail);

    if($checkEmail > 0){
        array_push($res, ['error' => true, 'message' => 'Email bạn nhập đã tồn tại!']);
        echo json_encode($res);
        die();
    }

    $password_md5 = md5($password);
    if($avatar['name'] == ''){
        $sqlInsert = "INSERT INTO users (user_name, user_email, user_password, user_tag) VALUES('$name', '$email', '$password_md5','$tag' )";
        $rlInsert = mysqli_query($conn, $sqlInsert);
        
    }else{

        if ($avatar['type'] != 'image/png' && $avatar['type'] != 'image/jpeg' && $avatar['type'] != 'image/gif') {
            array_push($res, ['error'=> true, 'message'=> 'Avatar bạn nhập không đúng định dạng (PNG, JPEG, GIF)']);
            echo json_encode($res);
            die();
        }
        $sqlInsert = "INSERT INTO users (user_name, user_email, user_password, user_avatar, user_tag) VALUES('$name', '$email', '$password_md5', '".$time.$avatar['name']."','$tag')";
        $rlInsert = mysqli_query($conn, $sqlInsert);

        move_uploaded_file($avatar['tmp_name'], '../images/user/'.$time.$avatar['name']);
    }

    $insert_id = mysqli_insert_id($conn);
    if($insert_id > 0){
        array_push($res, ['error'=> false, 'message'=> 'Đăng ký thành công']);
        echo json_encode($res);
        die();
    }else{
        array_push($res, ['error'=> true, 'message'=> 'Đăng ký thất bại']);
        echo json_encode($res);
        die();
    }
?>
