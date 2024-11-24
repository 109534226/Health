<?php
session_start();
// 引入資料庫連接設定
include "db.php";

// 查詢資料表
$sql = "SELECT id, title, subtitle, source, url, image FROM article";
$result = $link->query($sql);

// 如果查詢失敗
if (!$result) {
    die("SQL 查詢錯誤: " . $link->error);
}
// 禁止瀏覽器緩存頁面
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 確保用戶已經登入，否則重定向到登入頁面
if (!isset($_SESSION["登入狀態"]) || $_SESSION["登入狀態"] !== true) {
    echo "<script>
            alert('你還沒有登入，請先登入帳號。');
            window.location.href = 'login.php';
          </script>";
    exit();
}

// 檢查 "帳號" 和 "姓名" 是否存在於 $_SESSION 中
if (isset($_SESSION["帳號"]) && isset($_SESSION["姓名"])) {
    // 獲取用戶帳號和姓名
    $帳號 = $_SESSION['帳號'];
    $姓名 = $_SESSION['姓名'];
} else {
    echo "<script>
            alert('會話過期或資料遺失，請重新登入。');
            window.location.href = 'login.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>健康醫療網站</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&family=Roboto:wght@400;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* 彈出對話框的樣式 */
        .logout-box {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            display: none;
            z-index: 9999;
        }

        .logout-dialog {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .logout-dialog button {
            margin: 10px;
        }

        .navbar {
            margin-bottom: 0;
        }

        .navbar-collapse {
            margin: 0;
            padding: 0;
        }
    </style>
    <script>
        // 用戶成功登入後，設置登錄狀態
        sessionStorage.setItem('isLoggedIn', 'true');
    </script>
</head>

<body>
    <!-- 頁首 Start -->
    <!DOCTYPE html>
    <html lang="zh-Hant">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>文章管理</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }

            img {
                max-width: 100px;
                height: auto;
            }

            button {
                padding: 5px 10px;
                background-color: red;
                color: white;
                border: none;
                cursor: pointer;
                border-radius: 5px;
            }

            button:hover {
                background-color: darkred;
            }
        </style>
    </head>

    <body>
        <h1>文章管理</h1>
        <form method="POST" action="刪除文章的後端.php" onsubmit="return confirm('確定要刪除選中的文章嗎？');">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"> 全選</th>
                        <th>ID</th>
                        <th>標題</th>
                        <th>副標題</th>
                        <th>來源</th>
                        <th>連結</th>
                        <th>圖片</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?= $row['id']; ?>" class="itemCheckbox"></td>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['title']); ?></td>
                                <td><?= htmlspecialchars($row['subtitle']); ?></td>
                                <td><?= htmlspecialchars($row['source']); ?></td>
                                <td><a href="<?= htmlspecialchars($row['url']); ?>" target="_blank">連結</a></td>
                                <td><img src="<?= htmlspecialchars($row['image']); ?>" alt="圖片"></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">目前無資料</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <script>
                // 全選/取消全選功能
                function toggleSelectAll() {
                    var selectAllCheckbox = document.getElementById('selectAll');
                    var checkboxes = document.querySelectorAll('.itemCheckbox');
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                }
            </script>


            <br>
            <button type="submit">刪除選中文章</button>
        </form>

        <script>
            function toggleSelectAll() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('input[name="ids[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = selectAllCheckbox.checked);
            }
        </script>
    </body>

    </html>