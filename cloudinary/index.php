<?php  
require 'vendor/autoload.php';
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;

Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dm4utvg9k', 
        'api_key' => '869153385284368', 
        'api_secret' => '7ntLYrCC9HBCGfASOKYAUNwt7r0'],
    'url' => [
        'secure' => true
        ]
    ]);

function cloudinary_upload ($file_url='', $folder='videos', $type='video') {
    $data=[
        'error'=>true,
        'message'=>'',
        'data'=>null,
    ];
    if ($file_url == '') {
        $data['message'] = 'Chưa có video';
        return $data;
    }
    try {
        $result = (new UploadApi())->upload($file_url, [
        'folder' => 'videos',
        'resource_type' => $type]);

    } catch (Exception $e) {
        $data['message']=$e->getMessage();
        return $data;
    }
    $json_string = json_encode($result, JSON_PRETTY_PRINT);
    $result_array = json_decode($json_string, true);
    $data['error']=false;
    $data['data']=$result_array;
    return $data;
}
function cloudinary_delete ($public_ib='', $type='video') {
    $data=[
        'error'=>true,
        'message'=>'',
    ];

    if($public_ib == ''){
        $data['message'] = 'Chưa có dữ liệu public_ib';
        return $data;
    }

    try {
        $result = (new AdminApi())->deleteAssets($public_ib, ["resource_type" => $type, "type" => "upload"]);
    } catch (Exception $e) {
        $data['message']=$e->getMessage();
        return $data;
    }
    $jsonString = json_encode($result, JSON_PRETTY_PRINT);
    $resultArray = json_decode($jsonString, true);
    $deletedValue = $resultArray['deleted'][$public_ib];
    if ($deletedValue == 'deleted') {
        $data['error']=false;
        $data['message']='Xóa thành công';
        return $data;
    }else{
        $data['error']=true;
        $data['message']='Xóa thất bại';
        return $data;
    }   
}
?>