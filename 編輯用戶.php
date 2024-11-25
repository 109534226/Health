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
                        <a href="c_user.php" class="nav-item nav-link active">用戶管理</a>
                        <a href="c_content.php" class="nav-item nav-link ">內容管理</a>
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
    <?php
    session_start(); // 啟動 PHP Session，確保可以使用 $_SESSION 變數。
    include 'db.php'; // 引入資料庫連線檔案。
    
    // 從 URL 或 Session 中獲取用戶帳號 `name`，優先使用 URL 傳入的值。
    $name = $_GET['name'] ?? $_SESSION['編輯用戶'] ?? null;

    if (!$name) { // 如果沒有從 URL 或 Session 中獲得 `name`，則提示錯誤並終止程式。
        echo "未指定用戶帳號。"; // 顯示錯誤訊息。
        exit; // 終止程式執行。
    }

    // 從資料庫中查詢該用戶的所有資料，包括關聯的等級資料。
    $sql = "
    SELECT user.user_id, user.name, user.account, user.password, user.grade_id, grade.grade
    FROM user
    LEFT JOIN grade ON user.grade_id = grade.grade_id
    WHERE user.name = ?
";
    $stmt = mysqli_prepare($link, $sql); // 準備 SQL 查詢。
    mysqli_stmt_bind_param($stmt, "s", $name); // 綁定 `name` 參數到查詢語句，避免 SQL 注入。
    mysqli_stmt_execute($stmt); // 執行查詢。
    $result = mysqli_stmt_get_result($stmt); // 獲取查詢結果。
    $user = mysqli_fetch_assoc($result); // 將查詢結果轉換為關聯陣列形式。
    mysqli_stmt_close($stmt); // 關閉查詢語句。
    
    if (!$user) { // 如果查詢結果為空，表示找不到該用戶資料。
        echo "找不到該用戶資料！"; // 顯示錯誤訊息。
        exit; // 終止程式執行。
    }
    ?>

    <!-- 編輯使用者表單 -->
    <h1>編輯使用者</h1>
    <form method="POST" action="編輯用戶的後端.php"> <!-- 表單，提交到後端進行資料更新 -->
        <!-- 隱藏欄位，帶入用戶帳號 `name` 作為唯一標識 -->
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">

        <label for="name">姓名:</label> <!-- 顯示用戶姓名的輸入欄位 -->
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"><br>

        <label for="password">密碼:</label> <!-- 顯示用戶密碼的輸入欄位 -->
        <input type="password" name="password" value=""><br> <!-- 密碼欄位留空，要求用戶輸入新密碼 -->

        <label for="grade_id">使用者等級:</label> <!-- 顯示用戶等級的輸入欄位 -->
        <select name="grade_id">
            <option value="1" <?php if ($user['grade_id'] == 1)
                echo 'selected'; ?>>使用者</option>
            <option value="2" <?php if ($user['grade_id'] == 2)
                echo 'selected'; ?>>醫生</option>
            <option value="3" <?php if ($user['grade_id'] == 3)
                echo 'selected'; ?>>護士</option>
            <option value="4" <?php if ($user['grade_id'] == 4)
                echo 'selected'; ?>>醫院</option>
            <option value="5" <?php if ($user['grade_id'] == 5)
                echo 'selected'; ?>>管理者</option>
            <option value="6" <?php if ($user['grade_id'] == 6)
                echo 'selected'; ?>>未登入</option>
        </select><br>

        <button type="submit">更新</button> <!-- 表單提交按鈕 -->
    </form>

    <?php
    // 關閉資料庫連線
    mysqli_close($link);
    ?>


</body>

</html>