<?php
session_start(); // 啟動 Session
include "db.php"; // 載入資料庫連接

// 查詢健康相關資料，使用 type_id 進行關聯查詢
$sql_health = "SELECT article.title, article.subtitle, source.source, article.url, article.image FROM article 
               LEFT JOIN source ON article.source_id = source.source_id 
               WHERE article.type_id = 1 LIMIT 9";
$result_health = mysqli_query($link, $sql_health); // 執行健康相關資料查詢

// 查詢疾病相關資料，使用 type_id 進行關聯查詢
$sql_disease = "SELECT article.title, article.subtitle, source.source, article.url, article.image FROM article 
                LEFT JOIN source ON article.source_id = source.source_id 
                WHERE article.type_id = 2 LIMIT 9";
$result_disease = mysqli_query($link, $sql_disease); // 執行疾病相關資料查詢

// 構建一個陣列來存儲資料
$data = array(
    'health' => array(),
    'disease' => array()
);

// 迴圈處理健康相關資料
if (mysqli_num_rows($result_health) > 0) {
    while ($row = mysqli_fetch_assoc($result_health)) {
        $data['health'][] = $row; // 將健康相關資料添加到 data 陣列中
    }
}

// 迴圈處理疾病相關資料
if (mysqli_num_rows($result_disease) > 0) {
    while ($row = mysqli_fetch_assoc($result_disease)) {
        $data['disease'][] = $row; // 將疾病相關資料添加到 data 陣列中
    }
}

// 將結果以 JSON 格式返回給前端
header('Content-Type: application/json');
echo json_encode($data); // 將查詢結果轉換為 JSON 格式並輸出

// 關閉資料庫連接
mysqli_close($link); // 關閉資料庫連接
?>