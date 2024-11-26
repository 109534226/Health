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

// 查詢登入使用者的身份和姓名
$帳號 = $_SESSION['帳號'];
$sql = "SELECT grade_id, name FROM user WHERE account = ?";
$stmt = mysqli_prepare($link, $sql);
if (!$stmt) {
    die("查詢準備失敗：" . mysqli_error($link));
}
mysqli_stmt_bind_param($stmt, "s", $帳號);
mysqli_stmt_execute($stmt);
$結果 = mysqli_stmt_get_result($stmt);

if ($結果 && $row = mysqli_fetch_assoc($結果)) {
    // 設置角色
    if ($row['grade_id'] == 3) {
        $_SESSION['user_role'] = '醫生';
    } elseif ($row['grade_id'] == 2) {
        $_SESSION['user_role'] = '護士';
    } else {
        $_SESSION['user_role'] = '未知角色';
    }

    // 設置使用者姓名
    $_SESSION['name'] = $row['name'];
} else {
    echo "<script>alert('無法確定您的角色或名稱，請重新登入。'); window.location.href = 'login.php';</script>";
    exit();
}

// 確保角色和姓名已設定
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '未知角色';
$name = isset($_SESSION['name']) ? $_SESSION['name'] : '未知姓名';

// 取得所有醫院資料
$city = $_POST["city"];
$area = $_POST["area"];
$hospital = $_POST["hospital"];
$department = $_POST["department"];

// 建立 SQL 語句
if (!empty($hospital)) {
    $sql = "SELECT * FROM hospital h 
            JOIN medical m ON h.hospital_id = m.hospitalH_id
            JOIN user uA ON m.userA_id = uA.user_id
            JOIN user uN ON m.userN_id = uN.user_id
            WHERE h.city = ? AND h.area = ? AND h.hospital = ? AND h.department = ?";
    $stmt = mysqli_prepare($link, $sql);
    if (!$stmt) {
        die("查詢準備失敗：" . mysqli_error($link));
    }
    mysqli_stmt_bind_param($stmt, "ssss", $city, $area, $hospital, $department);
} else {
    $sql = "SELECT * FROM hospital h 
            JOIN medical m ON h.hospital_id = m.hospitalH_id
            JOIN user uA ON m.userA_id = uA.user_id
            JOIN user uN ON m.userN_id = uN.user_id
            WHERE h.city = ? AND h.area = ? AND h.department = ?";
    $stmt = mysqli_prepare($link, $sql);
    if (!$stmt) {
        die("查詢準備失敗：" . mysqli_error($link));
    }
    mysqli_stmt_bind_param($stmt, "sss", $city, $area, $department);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// 顯示搜尋結果
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . htmlspecialchars($row['hospital_id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($row['hospital'], ENT_QUOTES, 'UTF-8') . "</option>";
    }
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
                            class="nav-item nav-link">留言</a>
                        <a href="d_Basicsee.php" class="nav-item nav-link">患者資料</a>
                        <a href="d_recordssee.php" class="nav-item nav-link">看診紀錄</a>
                        <a href="d_timesee.php" class="nav-item nav-link">醫生的班表時段</a>
                        <a href="d_advicesee.php" class="nav-item nav-link active">醫生建議</a>
                        <div class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="d_profile.php" class="dropdown-item">關於我</a></li>
                                <li><a href="d_change.php" class="dropdown-item">忘記密碼</a></li>
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
   


    // 顯示當前角色
    echo "~歡迎回來~ " . htmlspecialchars($name) . "<br/>";
    echo "當前角色: " . htmlspecialchars($_SESSION['user_role']) . "</p>"; // 顯示當前角色
    echo "登入帳號: " . htmlspecialchars($_SESSION["帳號"]) . "</p>";
    ?>

    <!--醫生建議-->
    <div class="container-fluid"></div>
    <br />
    <section class="resume-section p-0" id="about"> <!-- 將內邊距設為 0 -->
        <div class="my-auto">
            <style>
                /* 搜尋 查看/填寫資料 */
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    text-align: center;
                    text-decoration: none;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                }

                .btn-primary {
                    background-color: #007bff;
                    /* 藍色背景 */
                    color: white;
                    /* 白色文字 */
                }

                .btn-primary:hover {
                    background-color: #0056b3;
                    /* 深藍色背景，懸停時 */
                }
            </style>
            <div class="d-flex justify-content-end mb-5">
                <a href="n_advicesee.php" class="btn btn-primary">查看資料</a>
            </div>
            <h1>
                <<<<<醫生建議>>>>>
            </h1>
            <br />
            <div class="form-container">
                <form action="醫生建議2.php" method="post" name="f1" onsubmit="return validateForm()">
                    <div class="form-row">
                        <label for="appointment_date">看診日期</label>
                        <input id="appointment_date" type="date" name="appointment_date"
                            min="<?php echo date('Y-m-d'); ?>" required />
                        <small>請選擇今天或未來的日期。</small>
                    </div>

                    <div class="form-row">
                        <label for="clinic_number">病例號</label>
                        <input id="clinic_number" type="text" name="clinic_number" required pattern="\d{1,10}"
                            title="請輸入1到10位數的病例號" />
                        <small>例：001 或 1234</small>
                    </div>

                    <div class="form-row">
                        <label for="patient_name">患者姓名</label>
                        <input id="patient_name" type="text" name="patient_name" required pattern=".{1,100}"
                            title="請輸入1到100字的姓名" />
                    </div>

                    <div class="form-row">
                        <label for="birth_date">出生年月日</label>
                        <input id="birth_date" type="date" name="birth_date" max="<?php echo date('Y-m-d'); ?>"
                            required />
                    </div>

                    <div class="form-row">
                        <label for="gender">性別</label>
                        <select id="gender" name="gender" required>
                            <option value="">選擇性別</option>
                            <option value="男">男</option>
                            <option value="女">女</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="doctor_name">看診醫生</label>
                        <input id="doctor_name" type="text" name="doctor_name" required pattern=".{1,100}"
                            title="請輸入1到100字的醫生姓名" />
                    </div>

                    <div class="form-row">
                        <label for="doctor_advice">醫生建議</label>
                        <input id="doctor_advice" type="text" name="doctor_advice" required pattern=".{1,255}"
                            title="請輸入1到255字的建議" />
                    </div>

                    <div class="form-row">
                        <label for="follow_up">是否回診</label>
                        <select id="follow_up" name="follow_up" required>
                            <option value="">選擇</option>
                            <option value="是">是</option>
                            <option value="否">否</option>
                        </select>
                    </div>

                    <button type="submit" class="aa">提交</button>
                </form>

                <script>
                    function validateForm() {
                        const birthDate = new Date(document.getElementById('birth_date').value);
                        const today = new Date();
                        // 檢查出生日期是否有效
                        if (birthDate >= today) {
                            alert('出生日期無效，請選擇過去的日期。');
                            return false;
                        }
                        return true; // 表單可以提交
                    }
                </script>
            </div>
        </div>
    </section>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .form-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            /* 設定最大寬度 */
            margin: auto;
            /* 讓表單居中 */
        }

        h1 {
            text-align: center;
            color: #343a40;
        }

        .form-row {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            /* 確保標籤在輸入框上方 */
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            /* 保持輸入框100%寬度 */
            padding: 10px;
            /* 調整內邊距 */
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        small {
            margin-top: 5px;
            font-size: 0.8em;
            /* 小字大小 */
            color: #6c757d;
            /* 小字顏色 */
        }

        .aa {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>

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