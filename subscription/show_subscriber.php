<?php 
include('../connect.php');
include('../jwt.php');
include('../library.php');

    $res = [
        'totalSubscriber'=>'',
        'page'=>'',
        'totalPage'=>'',
        'limit'=>'',
        'list'=>[]
    ];

    $limit = $_GET['_limit'] != '' ? (int)$_GET['_limit'] : 10;
    $page = $_GET['_page'] != '' ? (int)$_GET['_page'] : 1;
    $order_by = $_GET['_order_by'] != '' ? $_GET['_order_by'] : 'subscribe_id';
    $order_type = $_GET['_order_type'] != '' ? $_GET['_order_type'] : 'DESC';

    $headers = apache_request_headers();
    $token = $headers['access_token'];
    $token = str_replace('Bearer ', '', $token);
    $verify = verifyAccessToken($token);
    if ($verify['err']) {
        array_push($res, ['error'=>true, 'message'=>$verify['msg']]);
        echo json_encode($res);
        die();
    }
    $token_user_id = $verify['user']['user_id'];

    $sql = "SELECT subscribe_id FROM subscriptions WHERE subscribed_to_id='$token_user_id'";
    $rl = mysqli_query($conn, $sql);
    $res['totalSubscriber'] = mysqli_num_rows($rl);
    $res['limit'] = $limit;
    $res['page'] = $page;
    $res['totalPage'] = ceil($res['totalSubscriber'] / $res['limit']);
    $start = ($res['page'] - 1) * $res['limit'];

    $sql = "SELECT * FROM subscriptions INNER JOIN users ON subscriptions.subscriber_id=users.user_id WHERE subscribed_to_id='$token_user_id' ORDER BY $order_by $order_type LIMIT $start, $limit";
    $rl = mysqli_query($conn, $sql);

    while ( $row = mysqli_fetch_assoc($rl) ) {
        
        $subscribe = '';
        $user_id = $row['user_id'];

        $sql2 = "SELECT COUNT(subscribe_id) as total_subscribe FROM subscriptions WHERE subscribed_to_id='$user_id'";
        $rl2 = mysqli_query($conn, $sql2);
        $count_subscribe = mysqli_fetch_assoc($rl2);
        $subscribe = $count_subscribe['total_subscribe'];

        $sql3 = "SELECT subscribe_id FROM subscriptions WHERE subscriber_id='$token_user_id' AND subscribed_to_id='$user_id'";
        $rl3 = mysqli_query($conn, $sql3);
        $check_subscribe = mysqli_num_rows($rl3);

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
            'is_subscribed' => $check_subscribe > 0 ? true : false,
            'subscribe_created_at' => $subscribe_created_at,
            'subscribe_updated_at' => $subscribe_updated_at,
        ]);
    }

    echo json_encode($res);
?>