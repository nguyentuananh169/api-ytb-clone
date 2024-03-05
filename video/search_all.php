<?php 
	include('../connect.php');
	include('../library.php');
	include('../jwt.php');
	
	$res = [
		'totalResult' => '',
        'totalPage' => '',
        'page' => '',
        'limit' => '',
		'resultList'=> [],
	];
	
	$keyword = $_GET['_keyword'];
    $limit = isset($_GET['_limit']) && $_GET['_limit'] != '' ? (int)$_GET['_limit'] : 10;
    $page = isset($_GET['_page']) && $_GET['_page'] != '' ? (int)$_GET['_page'] : 1;

    $sql = "SELECT video_title AS result
FROM videos
WHERE video_title LIKE '%next%'
UNION
SELECT user_name AS result
FROM users
WHERE user_name LIKE '%next%'";
	$rl = mysqli_query($conn, $sql);
	$res['totalResult'] = mysqli_num_rows($rl);
	$res['limit'] = $limit;
    $res['page'] = $page;
    $res['totalPage'] = ceil($res['totalResult'] / $res['limit']);
    $start = ($res['page'] - 1) * $res['limit'];


	$sql2 = "SELECT videos.video_title, users.user_name
FROM videos
JOIN users ON videos.user_id = users.user_id
WHERE videos.video_title LIKE '%next%' OR users.user_name LIKE '%next%'";
	$rl2 = mysqli_query($conn, $sql2);

	while ( $row = mysqli_fetch_assoc($rl2) ) {
			
			$type = 'type_video';
			$user_id = $row['user_id'];
			$user_name = $row['user_name'];
			$user_avatar = $row['user_avatar'];
			$user_tag = $row['user_tag'];

			$category_id = $row['category_id'];
			$playlist_id = $row['playlist_id'];
			$video_id = $row['video_id'];
			$video_public_id = $row['video_public_id'];
			$video_title = $row['video_title'];
			$video_link = $row['video_link'];
			$video_poster = $row['video_poster'];
			$video_views = $row['video_views'];
			$video_des = $row['video_des'];
			$video_created_at = $row['video_created_at'];
			$video_updated_at = $row['video_updated_at'];

			array_push($res['resultList'], [
				'type' => $type,
				'user_id' => $user_id,
				'user_name' => $user_name,
				'user_avatar' => $user_avatar ? URLImgUser().$user_avatar : '',
				'user_tag' => $user_tag,
				'category_id' => $category_id,
				'playlist_id' => $playlist_id,
				'video_public_id' => $video_public_id,
				'video_id' => $video_id,
				'video_title' => $video_title,
				'video_link' => $video_link,
				'video_poster' => URLImgVideo().$video_poster,
				'video_views' => $video_views,
				'video_des' => $video_des,
				'video_created_at' => $video_created_at,
				'video_updated_at' => $video_updated_at,
			]);
		}
	
	echo "<pre>";
	var_dump($res);
	echo "</pre>";
	// echo json_encode($res);
?>