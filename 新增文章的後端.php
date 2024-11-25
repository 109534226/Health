<?php
session_start(); // 啟動 PHP Session，確保可以使用 $_SESSION 變數。
include 'db.php'; // 引入資料庫連線檔案。

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 從表單接收資料
    $title = $_POST["title"]; // 標題
    $subtitle = $_POST["subtitle"]; // 副標題
    $source = $_POST["source"]; // 資料來源
    $url = $_POST["url"]; // 連結
    $image = $_POST["image"]; // 圖片路徑
    $type_id = 1; // 預設文章類型為 1（健康），根據實際情況可以調整

    // 1. 檢查資料來源是否已存在於 `source` 表
    $sql_check_source = "SELECT source_id FROM source WHERE source = ?";
    $stmt = mysqli_prepare($link, $sql_check_source); // 準備 SQL 查詢
    mysqli_stmt_bind_param($stmt, "s", $source); // 綁定參數
    mysqli_stmt_execute($stmt); // 執行查詢
    $result = mysqli_stmt_get_result($stmt); // 獲取查詢結果

    if (mysqli_num_rows($result) > 0) {
        // 如果資料來源已存在，使用現有的 source_id
        $row = mysqli_fetch_assoc($result);
        $source_id = $row['source_id'];
    } else {
        // 如果資料來源不存在，插入新的資料來源
        $sql_insert_source = "INSERT INTO source (source) VALUES (?)";
        $stmt_insert = mysqli_prepare($link, $sql_insert_source);
        mysqli_stmt_bind_param($stmt_insert, "s", $source);
        if (mysqli_stmt_execute($stmt_insert)) {
            $source_id = mysqli_insert_id($link); // 獲取新插入的 source_id
        } else {
            die("資料來源插入失敗：" . mysqli_stmt_error($stmt_insert));
        }
        mysqli_stmt_close($stmt_insert); // 關閉語句
    }

    mysqli_stmt_close($stmt); // 關閉語句

    // 2. 插入新文章資料到 `article` 表
    $sql_insert_article = "
        INSERT INTO article (title, subtitle, source_id, url, image, type_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    $stmt_article = mysqli_prepare($link, $sql_insert_article); // 準備插入文章的 SQL 語句
    mysqli_stmt_bind_param($stmt_article, "ssissi", $title, $subtitle, $source_id, $url, $image, $type_id); // 綁定參數

    // 執行插入文章的語句並檢查結果
    if (mysqli_stmt_execute($stmt_article)) {
        echo "<script>
                alert('文章新增成功！');
                window.location.href = '文章列表.php'; // 跳轉到文章列表頁面
              </script>";
    } else {
        echo "<script>
                alert('新增文章失敗：" . mysqli_stmt_error($stmt_article) . "');
                window.location.href = '新增.php';
              </script>";
    }

    mysqli_stmt_close($stmt_article); // 關閉語句
}

// 關閉資料庫連線
mysqli_close($link);
?>
