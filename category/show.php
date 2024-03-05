<?php 
    include('../connect.php');
    $res = [];

    $sql = "SELECT * FROM category ORDER BY category_id DESC ";
    $rl = mysqli_query($conn, $sql);

    while($row = mysqli_fetch_assoc($rl)){
        $cate_id = $row['category_id'];
        $cate_name = $row['category_name'];
        $cate_status = $row['category_status'];
        $cate_created_at = $row['category_created_at'];
        $cate_updated_at = $row['category_updated_at'];
        array_push($res, ['id' => $cate_id, 'name' => $cate_name, 'status' => $cate_status, 'created' => $cate_created_at, 'updated' => $cate_updated_at]);
    }

    echo json_encode($res);

?>