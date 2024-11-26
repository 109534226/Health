<?php
session_start(); // 啟動 Session，讓伺服器能夠追蹤使用者的登入狀態
include "db.php"; // 引入資料庫連線檔案

// 確認使用者是否已登入
if (!isset($_SESSION["登入狀態"]) || $_SESSION["登入狀態"] !== true) {
    // 如果 Session 中沒有設定登入狀態，或狀態不為 true，則跳轉到登入頁面
    header("Location: login.php"); // 跳轉到登入頁面
    exit(); // 停止後續程式執行
}

// 從 Session 中獲取使用者的帳號
$帳號 = $_SESSION["帳號"]; // 取得使用者的帳號，通常在登入時已設置到 Session 中

// 設置 HTTP 標頭，防止頁面被瀏覽器緩存
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // 不允許瀏覽器緩存頁面內容
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // 設定頁面已過期的時間為一個很早的時間點
header("Pragma: no-cache"); // HTTP/1.0 的緩存控制，強制不緩存

?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <title>健康醫療網站</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Health Website" name="keywords">
    <meta content="Health Website" name="description">

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

    <!-- 自訂樣式 -->
    <style>
        /* 背景配圖與漸變顏色 */
        body {
            background: linear-gradient(to right, #e0f7fa, #b2ebf2);
            background-size: cover;
            font-family: 'Roboto', sans-serif;
        }

        /* 表單容器樣式 */
        .form-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border: 2px solid #d0e2e5;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
        }

        /* 標題樣式 */
        .form-container h2 {
            text-align: center;
            color: #009688;
            font-size: 28px;
            margin-bottom: 30px;
            font-weight: 700;
        }

        /* 表單字段樣式 */
        .form-row {
            margin-bottom: 20px;
        }

        .form-row label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #00796b;
        }

        .form-row input {
            width: 100%;
            padding: 12px;
            border: 2px solid #b0bec5;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f1f8e9;
            transition: all 0.3s ease;
        }

        .form-row input:focus {
            border-color: #00796b;
            background-color: #e8f5e9;
            outline: none;
        }

        /* 按鈕樣式 */
        .form-buttons {
            text-align: center;
        }

        .form-buttons button {
            padding: 12px 24px;
            background-color: #009688;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .form-buttons button:hover {
            background-color: #00796b;
            transform: translateY(-2px);
        }

        .form-buttons button:active {
            transform: translateY(0);
        }

        /* 圓形圖片樣式 */
        .profile-picture {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 4px solid #009688;
            margin: 20px auto;
            display: block;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-size: cover;
            /* 让图片完全覆盖容器 */
            background-position: center;
            /* 确保图片居中显示 */
            background-repeat: no-repeat;
            /* 防止背景重复 */
            overflow: hidden;
            /* 隐藏溢出部分 */
        }

        .profile-picture:hover {
            opacity: 0.85;
        }

        /* 刪除頭像按鈕樣式 */
        .delete-avatar-button {
            background-color: #ff5252;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-avatar-button:hover {
            background-color: #e53935;
        }

        /* 隱藏的文件上傳 */
        #fileInput {
            display: none;
        }

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
    <div class="container-fluid sticky-top bg-white shadow-sm mb-5">
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-3 py-lg-0">
                <a href="index.html" class="navbar-brand">
                    <h1 class="m-0 text-uppercase text-primary"><i class="fa fa-clinic-medical me-2"></i>健康醫療網站</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="" class="nav-item nav-link">首頁</a>
                        <a href="" class="nav-item nav-link">相關醫療資訊</a>
                        <a href="" class="nav-item nav-link">預約及現場掛號人數</a>
                        <a href="" class="nav-item nav-link">患者故事與經驗分享</a>
                        <div class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="u_profile.php" class="dropdown-item">關於我</a></li>
                                <li><a href="u_change.php" class="dropdown-item">忘記密碼</a></li>
                                <li><a href="#" class="dropdown-item" onclick="showLogoutBox()">登出</a></li>
                                <li><a href="#" class="dropdown-item" onclick="showDeleteAccountBox()">刪除帳號</a></li>
                                <!-- 隱藏表單，用於提交刪除帳號請求 -->
                                <from id="deleteAccountForm" action="刪除.php" method="POST" style="display:none;">
                                    <input type="hidden" name="帳號" value="<?php echo $帳號; ?>">
                                    <input type="hidden" name="姓名" value="<?php echo $姓名; ?>">
                                </from>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- 頁首 End -->
    <!-- 回到頁首(Top 箭頭 -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- 登出對話框 Start -->
    <div id="logoutBox" class="logout-box">
        <div class="logout-dialog">
            <p>你確定要登出嗎？</p>
            <button onclick="logout()">確定</button>
            <button onclick="hideLogoutBox()">取消</button>
        </div>
    </div>
    <!-- 登出對話框 End -->

    <!-- 刪除帳號對話框 Start -->
    <div id="deleteAccountBox" class="logout-box">
        <div class="logout-dialog">
            <p>你確定要刪除帳號嗎？這個操作無法撤銷！</p>
            <button onclick="deleteAccount()">確定</button>
            <button onclick="hideDeleteAccountBox()">取消</button>
        </div>
    </div>
    <!-- 刪除帳號對話框 End -->

    <!-- JavaScript -->
    <script>

        function showLogoutBox() {
            document.getElementById('logoutBox').style.display = 'flex';
        }

        function hideLogoutBox() {
            document.getElementById('logoutBox').style.display = 'none';
        }

        function logout() {
            // 移除登入狀態
            sessionStorage.removeItem('isLoggedIn');
            // 跳轉到登出頁面
            window.location.href = '登出.php';
        }

        function showDeleteAccountBox() {
            document.getElementById('deleteAccountBox').style.display = 'flex';
        }

        function hideDeleteAccountBox() {
            document.getElementById('deleteAccountBox').style.display = 'none';
        }

        function deleteAccount() {
            document.getElementById('deleteAccountForm').submit();
        }
    </script>
    <?php
    // 檢查是否有錯誤訊息
    if (isset($_GET['error'])) {
        echo "<script>alert('" . $_GET['error'] . "');</script>";
        echo "<script>enableFields();</script>"; // 保持欄位開啟
    }

    // 檢查是否有成功訊息
    if (isset($_GET['success'])) {
        echo "<script>alert('" . $_GET['success'] . "');</script>";
        echo "<script>disableFields();</script>"; // 保持欄位鎖定
    }
    ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>