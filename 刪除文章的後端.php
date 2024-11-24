<?php
session_start();
include 'db.php';
// 檢查是否有提交的文章 ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids'])) {
    // 將選中的 ID 陣列轉換為逗號分隔的字串，並進行資料清理
    $ids = implode(',', array_map('intval', $_POST['ids']));

    // 刪除選中的文章
    $query = "DELETE FROM articles WHERE id IN ($ids)";
    if ($link->query($query) === TRUE) {
        echo "成功刪除選中的文章。";
    } else {
        echo "刪除文章時發生錯誤：" . $link->error;
    }
} else {
    echo "沒有選中任何文章。";
}

// 關閉資料庫連線
$link->close();
?>