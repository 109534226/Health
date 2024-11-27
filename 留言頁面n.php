<?php
include "db.php"; // 連接資料庫

// 查詢留言記錄資料
$查詢語句 = "
    SELECT 
        msg.*, 
        u.name AS 發送者姓名, 
        p.name AS 接收者姓名 
    FROM messenger msg
    LEFT JOIN medical med ON msg.medicalS_id = med.user_id
    LEFT JOIN `user` u ON msg.medicalS_id = u.user_id
    LEFT JOIN `user` p ON msg.medicalP_id = p.user_id
    WHERE msg.messenger_id = ?
";

// 準備並執行查詢
$查詢準備 = mysqli_prepare($link, $查詢語句);
mysqli_stmt_bind_param($查詢準備, "i", $留言ID);
mysqli_stmt_execute($查詢準備);
$查詢結果 = mysqli_stmt_get_result($查詢準備);

if (!$查詢結果) {
    die("查詢留言記錄失敗: " . mysqli_error($link));
}

//  顯示查詢結果
// if ($資料列 = mysqli_fetch_assoc($查詢結果)) {
//     echo "留言內容：" . htmlspecialchars($資料列['messenger']);
//     // 顯示其他相關訊息...
// } else {
//     echo "查無留言記錄。";
// }
?>



<input type="hidden" name="sender" value="<?php echo htmlspecialchars($_SESSION['user_role']); ?>">

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
                        <a href="留言頁面n.php?id=<?php echo htmlspecialchars($patient_id); ?>"
                            class="nav-item nav-link active">留言</a>
                        <a href="n_Basicsee.php" class="nav-item nav-link">患者基本資訊</a>
                        <a href="n_recordssee.php" class="nav-item nav-link">病例歷史紀錄</a>
                        <a href="n_timesee.php" class="nav-item nav-link">醫生的班表時段</a>
                        <a href="n_advicesee.php" class="nav-item nav-link">醫生建議</a>
                        <div class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="n_profile.php" class="dropdown-item">關於我</a></li>
                                <li><a href="n_change.php" class="dropdown-item">變更密碼</a></li>
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


    <!-- <h1>歡迎，<?php echo htmlspecialchars($user_name); ?></h1>
    <?php echo "當前角色: " . htmlspecialchars($_SESSION['user_role']) . "</p>"; // 顯示當前角色
    echo "登入帳號: " . htmlspecialchars($_SESSION["帳號"]) . "</p>"; ?> -->

    <!-- <br /> -->

    <h2>留言記錄</h2>
    <div id="messages">
        <?php if (mysqli_num_rows($留言結果) > 0): ?>
            <ul>
                <?php while ($row = mysqli_fetch_assoc($留言結果)): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($row['sender_name']); ?>
                            (<?php echo htmlspecialchars($row['sender_role']); ?>)</strong>
                        對
                        <strong><?php echo htmlspecialchars($row['receiver_name']); ?>
                            (<?php echo htmlspecialchars($row['receiver_role']); ?>)</strong>
                        說：
                        <?php echo htmlspecialchars($row['message']); ?>
                        <br>
                        <small><?php echo htmlspecialchars($row['message_time']); ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>目前沒有留言。</p>
        <?php endif; ?>
    </div>

    <h3>發送新留言</h3>
    <form method="POST" action="醫生護士互相留言處理n.php">
        <label for="receiver">接收者帳號：</label>
        <input type="text" id="receiver" name="receiver_account" required><br>
        <textarea name="message" rows="4" cols="50" placeholder="輸入您的留言" required></textarea><br>
        <button type="submit">送出</button>
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