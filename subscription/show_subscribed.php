<?php 
include('../connect.php');
include('../jwt.php');
include('../library.php');

    $res = [
        'totalSubscribe'=>'',
        'page'=>'',
        'totalPage'=>'',
        'limit'=>'',
        'list'=>[]
    ];

    $type = $_GET['_type'];
    $limit = isset($_GET['_limit']) && $_GET['_limit'] != '' ? (int)$_GET['_limit'] : 10;
    $page = isset($_GET['_page']) && $_GET['_page'] != '' ? (int)$_GET['_page'] : 1;

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

    $sql = "SELECT subscribe_id FROM subscriptions WHERE subscriber_id='$user_id'";
    $rl = mysqli_query($conn, $sql);
    $res['totalSubscribe'] = mysqli_num_rows($rl);
    $res['limit'] = $limit;
    $res['page'] = $page;
    $res['totalPage'] = ceil($res['totalSubscribe'] / $res['limit']);
    $start = ($res['page'] - 1) * $res['limit'];

    $sql = "SELECT * FROM subscriptions INNER JOIN users ON subscriptions.subscribed_to_id=users.user_id WHERE subscriber_id='$user_id' ORDER BY subscribe_id DESC LIMIT $start, $limit";
    $rl = mysqli_query($conn, $sql);

    while ( $row = mysqli_fetch_assoc($rl) ) {
        $video = '';
        $subscribe = '';
        $user_id = $row['user_id'];
        if ($type == 'user_statistical') {

            $sql2 = "SELECT COUNT(video_id) as total_video FROM videos WHERE user_id='$user_id'";
            $rl2 = mysqli_query($conn, $sql2);
            $count_video = mysqli_fetch_assoc($rl2);
            $video = $count_video['total_video'];

            $sql3 = "SELECT COUNT(subscribe_id) as total_subscribe FROM subscriptions WHERE subscribed_to_id='$user_id'";
            $rl3 = mysqli_query($conn, $sql3);
            $count_subscribe = mysqli_fetch_assoc($rl3);
            $subscribe = $count_subscribe['total_subscribe'];
        }

        $subscribe_id = $row['subscribe_id'];
        $subscriber_id = $row['subscriber_id'];
        $subscribe_created_at = $row['subscribe_created_at'];
        $subscribe_updated_at = $row['subscribe_updated_at'];
        $user_name = $row['user_name'];
        $user_avatar = $row['user_avatar'];
        $user_des = $row['user_des'];

        array_push($res['list'], [
            'subscribe_id' => $subscribe_id,
            'subscriber_id' => $subscriber_id,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_des' => $user_des,
            'user_avatar' => $user_avatar ? URLImgUser().$user_avatar : '',
            'user_total_subscribe' => $subscribe,
            'user_total_video' => $video,
            'subscribe_created_at' => $subscribe_created_at,
            'subscribe_updated_at' => $subscribe_updated_at,
        ]);
    }

    echo json_encode($res);
?>