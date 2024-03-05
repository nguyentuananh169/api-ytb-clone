<?php 
    include('../connect.php');
    include('../library.php');
    include('../jwt.php');
    
    $res = [
        'totalPlaylist' => '',
        'totalPage' => '',
        'page' => '',
        'limit' => '',
        'playlist'=> [],
    ];
    $params = '';
    $type = $_GET['type'] ? $_GET['type'] : '';
    $keyword = $_GET['keyword'] ? $_GET['keyword'] : '';
    $limit = $_GET['limit'] ? (int)$_GET['limit'] : 10;
    $page = $_GET['page'] ? (int)$_GET['page'] : 1;
    
    if ($type == 'get_by_token') {
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
        $params="WHERE user_id='$user_id'";
    }else if ($type=="get_by_id") {
        $params="WHERE playlist_id='$keyword'";
    }else if ($type=="search") {
        $params="WHERE playlist_name LIKE '%$keyword%'";
    }else if ($type=="user_id") {
        $params="WHERE user_id='$keyword'";
    }

    $sql = "SELECT playlist_id FROM playlists ".$params;
    $rl = mysqli_query($conn, $sql);
    $res['totalPlaylist'] = mysqli_num_rows($rl);
    $res['limit'] = $limit;
    $res['page'] = $page;
    $res['totalPage'] = ceil($res['totalPlaylist'] / $res['limit']);
    $start = ($res['page'] - 1) * $res['limit'];

    $sql2 = "SELECT * FROM playlists ".$params." ORDER BY playlist_id DESC LIMIT $start, $limit";
    $rl2 = mysqli_query($conn, $sql2);

    while ( $row = mysqli_fetch_assoc($rl2) ) {
        $playlist_id = $row['playlist_id'];
        $sql3 = "SELECT video_poster,category_id,video_id FROM videos WHERE playlist_id='$playlist_id' ORDER BY playlist_update_time ASC";
        $rl3 = mysqli_query($conn,$sql3);
        $count = mysqli_num_rows($rl3);
        $data = mysqli_fetch_assoc($rl3);

        $sql4 = "SELECT playlist_update_time FROM videos WHERE playlist_id='$playlist_id' ORDER BY playlist_update_time DESC LIMIT 0,1";
        $rl4 = mysqli_query($conn,$sql4);
        $time = mysqli_fetch_assoc($rl4);

        $playlist_name = $row['playlist_name'];
        $playlist_des = $row['playlist_des'];
        $playlist_created_at = $row['playlist_created_at'];
        $playlist_updated_at = $time['playlist_update_time'];

        array_push($res['playlist'], [
            'playlist_id' => $playlist_id,
            'total_video' => $count,
            'playlist_name' => $playlist_name,
            'playlist_des' => $playlist_des,
            'video_id'=>$data['video_id'],
            'video_category_id'=>$data['category_id'],
            'video_poster'=>$data['video_poster'] ? URLImgVideo().$data['video_poster'] : '',
            'playlist_created_at' => $playlist_created_at,
            'playlist_updated_at' => $playlist_updated_at,
        ]);
    }

    echo json_encode($res);
?>