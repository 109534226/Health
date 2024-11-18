<?php
session_start();
include "db.php";

// 获取用户账号
$帳號 = $_SESSION["帳號"];

// 检查是否有上传的头像文件
if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
    // 获取图片数据
    $imageData = file_get_contents($_FILES['profilePicture']['tmp_name']);
    $encodedImage = mysqli_real_escape_string($link, $imageData);

    // 更新数据库头像
    $SQL更新 = "UPDATE people SET image='$encodedImage' WHERE name='$帳號'";
    if (mysqli_query($link, $SQL更新)) {
        // 返回 Base64 编码的图像数据
        $imageBase64 = 'data:image/jpeg;base64,' . base64_encode($imageData);
        echo json_encode(['success' => true, 'imageUrl' => $imageBase64]);
    } else {
        echo json_encode(['success' => false, 'error' => '数据库更新失败']);
    }
} else {
    echo json_encode(['success' => false, 'error' => '文件上传失败']);
}
?>
