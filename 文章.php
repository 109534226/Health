<?php
session_start();
include "db.php"; // 載入資料庫連接

// 查詢健康相關資料
$sql_health = "SELECT title, subtitle, source, url, image FROM article WHERE type = 1 LIMIT 9";
$result_health = mysqli_query($link, $sql_health);

// 查詢疾病相關資料
$sql_disease = "SELECT title, subtitle, source, url, image FROM article WHERE type = 2 LIMIT 9";
$result_disease = mysqli_query($link, $sql_disease);

// 構建一個陣列來存儲資料
$data = array(
    'health' => array(),
    'disease' => array()
);

// 迴圈處理健康相關資料
if (mysqli_num_rows($result_health) > 0) {
    while ($row = mysqli_fetch_assoc($result_health)) {
        $data['health'][] = $row;
    }
}

// 迴圈處理疾病相關資料
if (mysqli_num_rows($result_disease) > 0) {
    while ($row = mysqli_fetch_assoc($result_disease)) {
        $data['disease'][] = $row;
    }
}

// 將結果以 JSON 格式返回給前端
header('Content-Type: application/json');
echo json_encode($data);

// 關閉資料庫連接
mysqli_close($link);
?>
