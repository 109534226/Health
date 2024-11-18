<?php
session_start();

// 禁止瀏覽器緩存頁面
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include "db.php";  // 包含資料庫連接

// 確認使用者是否已登入
if (!isset($_SESSION["登入狀態"]) || $_SESSION["登入狀態"] !== true) {
    echo "<script>alert('你還沒有登入，請先登入帳號。'); window.location.href = 'login.php';</script>";
    exit();
}

// 確認帳號是否存在於 Session 中
if (isset($_SESSION["帳號"])) {
    $帳號 = $_SESSION['帳號'];
} else {
    echo "<script>alert('會話過期或資料遺失，請重新登入。'); window.location.href = 'login.php';</script>";
    exit();
}

// 查詢登入使用者的身份（醫生或護士）
$查詢角色 = "SELECT grade FROM user WHERE username = '$帳號'";
$角色結果 = mysqli_query($link, $查詢角色);

if ($角色結果 && $row = mysqli_fetch_assoc($角色結果)) {
    if ($row['grade'] == 1) {
        $_SESSION['user_role'] = '醫生';
    } elseif ($row['grade'] == 2) {
        $_SESSION['user_role'] = '護士';
    }
    echo "<script>console.log('角色設定為: " . $_SESSION['user_role'] . "');</script>"; // 調試訊息
} else {
    echo "<script>alert('無法確定您的角色，請重新登入。'); window.location.href = 'login.php';</script>";
    exit();
}

// 使用 Session 中的 user_role 確保顯示正確角色
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '醫生';

// 取得所有來自醫生和護士的留言，按時間排序
$查詢指令 = "SELECT sender, message, timestamp FROM chatmessages WHERE sender IN ('醫生', '護士') ORDER BY timestamp DESC";
$查詢結果 = mysqli_query($link, $查詢指令);

if (!$查詢結果) {
    die("查詢失敗: " . mysqli_error($link));
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
    <!-- 設置每 5 秒自動刷新頁面 -->
    <meta http-equiv="refresh" content="5">
</head>

<body>
    <!-- 頁首 Start -->
    <div class="container-fluid sticky-top bg-white shadow-sm mb-5">
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
                        <a href="留言介面d.php" class="nav-item nav-link active">留言</a>
                        <a href="d_Basicsee.php" class="nav-item nav-link">患者基本資訊</a>
                        <a href="d_recordssee.php" class="nav-item nav-link">病例歷史紀錄</a>
                        <a href="d_timesee.php" class="nav-item nav-link">醫生的班表時段</a>
                        <a href="d_advicesee.php" class="nav-item nav-link">醫生建議</a>
                        <div class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="d_profile.php" class="dropdown-item">關於我</a></li>
                                <li><a href="d_change.php" class="dropdown-item">變更密碼</a></li>
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

    <?php
    echo "<p>當前角色: " . htmlspecialchars($user_role) . "</p>"; // 顯示當前角色作為檢查
    ?>

    <h2>醫生與護士的留言記錄</h2>

    <!-- 顯示留言記錄 -->
    <div id="messageHistory">
        <?php if (mysqli_num_rows($查詢結果) > 0): ?>
            <ul>
                <?php while ($row = mysqli_fetch_assoc($查詢結果)): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($row['sender']); ?>:</strong>
                        <?php echo htmlspecialchars($row['timestamp']) . " - " . htmlspecialchars($row['message']); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>尚無留言。</p>
        <?php endif; ?>
    </div>

    <!-- 留言輸入表單 -->
    <h3>發送新留言</h3>
    <form method="POST" action="醫生護士互相留言處理d.php">
        <!-- 使用隱藏欄位設置 sender 的值為 session 中的 user_role -->
        <input type="hidden" name="sender" value="<?php echo htmlspecialchars($_SESSION['user_role']); ?>">
        <textarea name="message" rows="4" cols="50" placeholder="請輸入留言"></textarea><br>
        <button type="submit">送出留言</button>
    </form>


    <!-- 回到頁首(Top 箭頭 -->
    <a href="#" class="btn btn-lg btn-primary  back-to-top"><i class="bi bi-arrow-up"></i></a>

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
            alert('你已經登出！');
            hideLogoutBox();
            window.location.href = 'login.php'; // 替換為登出後的頁面
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

<?php
// 關閉資料庫連線
mysqli_close($link);
?>