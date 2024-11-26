<html>
<?php
session_start();

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
    <div class="container-fluid sticky-top bg-white shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-3 py-lg-0">
                <a href="n_profile.php" class="navbar-brand">
                    <h1 class="m-0 text-uppercase text-primary"><i class="fa fa-clinic-medical me-2"></i>健康醫療網站</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="c_user.php" class="nav-item nav-link ">用戶管理</a>
                        <a href="c_content.php" class="nav-item nav-link active">內容管理</a>
                        <a href="c_security.php" class="nav-item nav-link">安全管理</a>

                        <div class="nav-item">
                            <a href="c_profile.php" class="nav-link dropdown-toggle " data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="c_profile.php" class="dropdown-item ">關於我</a></li>
                                <li><a href="c_change.php" class="dropdown-item">忘記密碼</a></li>
                                <li><a href="#" class="dropdown-item" onclick="showLogoutBox()">登出</a></li>
                                <li><a href="#" class="dropdown-item" onclick="showDeleteAccountBox()">刪除帳號</a></li>
                                <!-- 隱藏表單，用於提交刪除帳號請求 -->
                                <form id="deleteAccountForm" action="刪除.php" method="POST" style="display:none;">
                                    <input type="hidden" name="帳號" value="<?php echo $帳號; ?>">
                                    <input type="hidden" name="姓名" value="<?php echo $姓名; ?>">
                                </form>
                            </ul>
                        </div>

                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- 頁首 End -->
    <!-- 頁首 End -->

    </form>
    </div>

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

        <!DOCTYPE html>
        <html lang="zh">

        <head>
            <style>
                /* 調整文字框樣式 */
                .text-box {
                    width: 100%;
                    /* 自適應寬度 */
                    height: 40px;
                    /* 固定高度 */
                    border: 1px solid black;
                    padding: 5px;
                    /* 增加邊距使文字不貼著邊框 */
                    font-size: 16px;
                    /* 調整字體大小 */
                    font-family: Arial, sans-serif;
                    /* 設定字體 */
                    box-sizing: border-box;
                    /* 包括內邊距和邊框在寬度內 */
                }

                /* 特別設計內容框的樣式 */
                .content-box {
                    width: 100%;
                    /* 自適應寬度 */
                    height: 150px;
                    /* 初始高度 */
                    border: 1px solid black;
                    padding: 10px;
                    font-size: 16px;
                    font-family: Arial, sans-serif;
                    line-height: 1.5;
                    /* 調整行距 */
                    resize: both;
                    /* 允許手動調整大小 */
                    overflow: auto;
                    /* 顯示滾動條 */
                    box-sizing: border-box;
                }

                /* 表單標籤樣式 */
                label {
                    display: block;
                    margin-top: 10px;
                    /* 與上方元素保持距離 */
                    font-weight: bold;
                }

                /* 置中按鈕容器 */
                .button-container {
                    display: flex;
                    justify-content: center;
                    /* 水平置中 */
                    margin-top: 20px;
                    /* 與上方表單元素保持距離 */
                }

                /* 提交按鈕樣式 */
                .submit-button {
                    padding: 10px 20px;
                    font-size: 16px;
                    font-family: Arial, sans-serif;
                    cursor: pointer;
                    border: 1px solid #000;
                    background-color: #f0f0f0;
                    transition: background-color 0.3s;
                }

                .submit-button:hover {
                    background-color: #ddd;
                }
            </style>
        </head>

        <body>
            <!-- 資料表是關聯性資料庫要注意！！ -->
                <!-- 內容表單 -->
    <div class="container mt-4">
        <form method="POST" action="新增文章的後端.php">
            <label for="title">標題:</label>
            <input type="text" id="title" name="title" class="text-box" placeholder="請輸入標題" required>

            <label for="subtitle">內容:</label>
            <textarea id="subtitle" name="subtitle" class="content-box" placeholder="請輸入內容" required></textarea>

            <label for="source">資料來源:</label>
            <select id="source" name="source" class="dropdown" required>
                <option value="">請選擇資料來源</option>
                <option value="1">天下雜誌</option>
                <option value="2">銀天下</option>
            </select>

            <label for="url">連結:</label>
            <input type="url" id="url" name="url" class="text-box" placeholder="請輸入連結" required>

            <label for="image">圖片路徑:</label>
            <input type="text" id="image" name="image" class="text-box" placeholder="請輸入圖片路徑" required>

            <label for="review">審核結果:</label>
            <select id="review" name="review" class="dropdown" required>
                <option value="">未選擇</option>
                <option value="1">已審核</option>
                <option value="2">未審核</option>
            </select>
                <!-- 新增文章類型欄位 資料表是 type -->

                <div class="button-container">
                    <button type="submit" class="submit-button">提交</button>
                </div>
            </form>
        </body>

        </html>