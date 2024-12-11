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
    <meta http-equiv="refresh" content="10">
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
                        <a href="留言頁面d.php?id=<?php echo htmlspecialchars($patient_id); ?>"
                            class="nav-item nav-link active">留言</a>
                        <a href="d_Basicsee.php" class="nav-item nav-link">患者資料</a>
                        <a href="d_recordssee.php" class="nav-item nav-link">看診紀錄</a>
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


    <!-- <h1>歡迎，<?php echo htmlspecialchars($user_name); ?></h1>
    <?php echo "當前角色: " . htmlspecialchars($_SESSION['user_role']) . "</p>"; // 顯示當前角色
    echo "登入帳號: " . htmlspecialchars($_SESSION["帳號"]) . "</p>"; ?>

    <br /> -->


    <?php
    include "db.php";

    // 查詢留言記錄，包含關聯表的詳細資訊
    $query = "
    SELECT 
        m.messenger_id,
        s_medical.medical_id AS sender_medical_id,
        s_hospital.hospital AS sender_hospital_name,
        s_department.department AS sender_department_name,
        s_user.name AS sender_name,
        s_user.grade_id AS sender_role_id,
        r_medical.medical_id AS receiver_medical_id,
        r_hospital.hospital AS receiver_hospital_name,
        r_department.department AS receiver_department_name,
        r_user.name AS receiver_name,
        r_user.grade_id AS receiver_role_id,
        m.messenger AS message,
        m.timestamp AS message_time,
        sg.grade AS sender_role,
        rg.grade AS receiver_role
    FROM messenger m
    JOIN medical s_medical ON m.medicalS_id = s_medical.medical_id
    JOIN user s_user ON s_medical.user_id = s_user.user_id
    JOIN department s_department ON s_medical.department_id = s_department.department_id
    JOIN hospital s_hospital ON s_department.hospital_id = s_hospital.hospital_id
    JOIN grade sg ON s_user.grade_id = sg.grade_id
    JOIN medical r_medical ON m.medicalP_id = r_medical.medical_id
    JOIN user r_user ON r_medical.user_id = r_user.user_id
    JOIN department r_department ON r_medical.department_id = r_department.department_id
    JOIN hospital r_hospital ON r_department.hospital_id = r_hospital.hospital_id
    JOIN grade rg ON r_user.grade_id = rg.grade_id
    ORDER BY m.timestamp DESC
";

    $留言結果 = $link->query($query);
    ?>
    <h2>留言記錄</h2>
    <div id="messages">
        <?php if (mysqli_num_rows($留言結果) > 0): ?>
            <ul>
                <?php while ($row = mysqli_fetch_assoc($留言結果)): ?>
                    <li>
                        (留言時間:
                        <small><?php echo htmlspecialchars($row['message_time']); ?></small>)
                        <br />
                        <strong>
                            <?php echo htmlspecialchars($row['sender_name']); ?>
                            (<?php echo htmlspecialchars($row['sender_role']); ?>,
                            <?php echo htmlspecialchars($row['sender_hospital_name']); ?>,
                            <?php echo htmlspecialchars($row['sender_department_name']); ?>)
                        </strong>
                        對
                        <strong>
                            <?php echo htmlspecialchars($row['receiver_name']); ?>
                            (<?php echo htmlspecialchars($row['receiver_role']); ?>,
                            <?php echo htmlspecialchars($row['receiver_hospital_name']); ?>,
                            <?php echo htmlspecialchars($row['receiver_department_name']); ?>)
                        </strong>
                        說：
                        <?php echo htmlspecialchars($row['message']); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>目前沒有留言。</p>
        <?php endif; ?>
    </div>

    <br />

    <?php
    session_start(); // 確保 session 已啟用
    include "db.php"; // 資料庫連接

    $logged_in_account = $_SESSION['帳號'];

    // 查詢所有醫生和護士，排除當前登入的使用者
    $查詢接收者 = "SELECT `user_id`, `name`, `account` FROM user WHERE grade_id IN (2, 3) AND account != '$logged_in_account'";
    $接收者結果 = mysqli_query($link, $查詢接收者);
    ?>

    <h3>發送新留言</h3>
    <form id="messageForm" method="POST" action="醫生護士互相留言處理d.php">
        <div style="margin-bottom: 10px;">
            <label for="receiver">接收者姓名：</label>
            <select id="receiver" name="receiver_name" required style="width: 300px;">
                <option value="" disabled selected>選擇接收者姓名</option>
                <?php
                if ($接收者結果 && mysqli_num_rows($接收者結果) > 0) {
                    while ($行 = mysqli_fetch_assoc($接收者結果)) {
                        echo '<option value="' . htmlspecialchars($行['account']) . '">' . htmlspecialchars($行['name']) . '</option>';
                    }
                } else {
                    echo '<option value="" disabled>沒有可選擇的接收者</option>';
                }
                ?>
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="message">輸入您的留言：</label><br>
            <textarea id="message" name="message" rows="4" cols="50" placeholder="輸入您的留言內容" required
                style="width: 300px;"></textarea><br>
        </div>

        <button type="submit">送出</button>
        <button type="reset">取消</button>
    </form>



    <!-- <?php
    include "db.php"; // 資料庫連接
    
    // 查詢所有醫生和護士
    $查詢接收者 = "SELECT `user_id`, `name` , `account` FROM user WHERE grade_id IN (2, 3)"; // 2 代表醫生，3 代表護士
    $接收者結果 = mysqli_query($link, $查詢接收者);
    ?>

<h3>發送新留言</h3>
    <form id="messageForm" method="POST" action="醫生護士互相留言處理d.php">
        <div style="margin-bottom: 10px;">
            <label for="receiver">接收者姓名：</label>
            <select id="receiver" name="receiver_name" required style="width: 300px;">
                <option value="" disabled selected>選擇接收者姓名</option>
                <?php
                if ($接收者結果 && mysqli_num_rows($接收者結果) > 0) {
                    while ($行 = mysqli_fetch_assoc($接收者結果)) {
                        echo '<option value="' . htmlspecialchars($行['account']) . '">' . htmlspecialchars($行['name']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="message">輸入您的留言：</label><br>
            <textarea id="message" name="message" rows="4" cols="50" placeholder="輸入您的留言內容" required
                style="width: 300px;"></textarea><br>
        </div>

        <button type="submit">送出</button>
        <button type="reset">取消</button>
    </form> -->

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