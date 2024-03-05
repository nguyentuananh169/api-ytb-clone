<?php 
    include('../connect.php');
    include('../library.php');
    
    $res = [
        'totalPlaylist' => '',
        'totalPage' => '',
        'page' => '',
        'limit' => '',
        'playlist'=> [],
    ];
    $keyword = $_GET['_keyword'] ? $_GET['_keyword'] : '';
    $limit = $_GET['_limit'] ? (int)$_GET['_limit'] : 10;
    $page = $_GET['_page'] ? (int)$_GET['_page'] : 1;

    if ($keyword == '') {
        array_push($res, ['error'=>true, 'message'=>'Bạn chưa nhập từ khóa tìm kiếm']);
        echo json_encode($res);
        die();
    }

    $params="WHERE playlists.playlist_name LIKE '%$keyword%'";
    
    $sql = "SELECT COUNT(DISTINCT playlists.playlist_id) AS total FROM playlists INNER JOIN videos ON playlists.playlist_id = videos.playlist_id $params";
    $rl = mysqli_query($conn, $sql);
    $count_playlist = mysqli_fetch_assoc($rl);
    $res['totalPlaylist'] = $count_playlist['total'];
    $res['limit'] = $limit;
    $res['page'] = $page;
    $res['totalPage'] = ceil($res['totalPlaylist'] / $res['limit']);
    $start = ($res['page'] - 1) * $res['limit'];

    $sql2 = "SELECT DISTINCT playlists.playlist_id,playlists.playlist_name,playlists.playlist_des,playlists.playlist_created_at,playlists.playlist_updated_at,users.user_id,users.user_name FROM playlists INNER JOIN videos ON playlists.playlist_id = videos.playlist_id INNER JOIN users ON playlists.user_id = users.user_id $params ORDER BY playlists.playlist_id DESC LIMIT $start, $limit";
    $rl2 = mysqli_query($conn, $sql2);

    while ( $row = mysqli_fetch_assoc($rl2) ) {
        $playlist_id = $row['playlist_id'];
        $sql3 = "SELECT video_poster, category_id, video_id, video_duration, video_title FROM videos WHERE playlist_id='$playlist_id' ORDER BY playlist_update_time ASC LIMIT 0, 2";
        $rl3 = mysqli_query($conn,$sql3);
        $data = [];

        while ($row2 = mysqli_fetch_assoc($rl3)) {
            array_push($data,[
                'video_id' => $row2['video_id'],
                'category_id' => $row2['category_id'],
                'video_poster' => URLImgVideo().$row2['video_poster'],
                'video_duration' => $row2['video_duration'],
                'video_title' => $row2['video_title']
            ]);
        }

        $sql4 = "SELECT COUNT(videos.video_id) AS total_video FROM videos WHERE playlist_id='$playlist_id'";
        $rl4 = mysqli_query($conn,$sql4);
        $count = mysqli_fetch_assoc($rl4);

        $playlist_name = $row['playlist_name'];
        $playlist_des = $row['playlist_des'];
        $playlist_created_at = $row['playlist_created_at'];
        $playlist_updated_at = $row['playlist_updated_at'];
        $user_id = $row['user_id'];
        $user_name = $row['user_name'];

        array_push($res['playlist'], [
            'playlist_id' => $playlist_id,
            'total_video' => $count['total_video'],
            'playlist_name' => $playlist_name,
            'playlist_des' => $playlist_des,
            'playlist_created_at' => $playlist_created_at,
            'playlist_updated_at' => $playlist_updated_at,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'videoList' => $data
        ]);
    }

    echo json_encode($res);
?>