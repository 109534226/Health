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
                        <a href="n_Basic.php" class="nav-item nav-link active">患者資料</a>
                        <a href="n_records.php" class="nav-item nav-link">看診紀錄</a>
                        <a href="n_time.php" class="nav-item nav-link">醫生的班表時段</a>
                        <a href="n_advice.php" class="nav-item nav-link">醫生建議</a>
                        <div class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="n_profile.php" class="dropdown-item">關於我</a></li>
                                <li><a href="n_change.php" class="dropdown-item">忘記密碼</a></li>
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
            <h1>
                ----<患者資料修改>----
            </h1>
            <br />

            <?php
            include "db.php";

            if (isset($_POST['id'])) {
                $id = intval($_POST['id']);

                // 從資料庫取得資料
                $sql = "SELECT * FROM patients WHERE id = $id";
                $result = mysqli_query($link, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                } else {
                    echo "<script>
                        alert('無法找到指定ID的資料。');
                        window.location.href = 'n_Basicsee.php';
                    </script>";
                    exit;
                }
            } else {
                echo "<script>
                    alert('未提供有效的ID。');
                    window.location.href = 'n_Basicsee.php';
                    </script>";
                exit;
            }
            ?>
            <div class="form-container">
                <!-- 表單顯示及更新 -->
                <form id="updateForm" action="患者資料修改1.php" method="post" onsubmit="return confirmUpdate()">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

                    <label>病例號:</label>
                    <input type="text" id="medical_record_number" name="medical_record_number"
                        value="<?php echo htmlspecialchars($row['medicalnumber']); ?>" required><br>

                    <label>患者姓名:</label>
                    <input type="text" id="patient_name" name="patient_name"
                        value="<?php echo htmlspecialchars($row['patientname']); ?>" required><br>

                    <label>性別:</label>
                    <select id="gender" name="gender" required>
                        <option value="男" <?php echo $row['gender'] == '男' ? 'selected' : ''; ?>>男</option>
                        <option value="女" <?php echo $row['gender'] == '女' ? 'selected' : ''; ?>>女</option>
                    </select><br>

                    <label>出生年月日:</label>
                    <input type="date" id="birth_date" name="birth_date"
                        value="<?php echo htmlspecialchars($row['birthdaydate']); ?>" required><br>

                    <label>當前症狀:</label>
                    <textarea id="current_symptoms" name="current_symptoms"
                        required><?php echo htmlspecialchars($row['currentsymptoms']); ?></textarea><br>

                    <label>過敏藥物:</label>
                    <textarea id="allergies"
                        name="allergies"><?php echo htmlspecialchars($row['allergies']); ?></textarea><br>

                    <label>歷史重大疾病:</label>
                    <textarea id="medical_history"
                        name="medical_history"><?php echo htmlspecialchars($row['medicalhistory']); ?></textarea><br>

                    <button type="submit" class="aa">更新</button>
                </form>

                <script>
                    function confirmUpdate() {
                        const medicalRecord = document.getElementById("medical_record_number").value;
                        const patientName = document.getElementById("patient_name").value;
                        const gender = document.getElementById("gender").value;
                        const birthDate = document.getElementById("birth_date").value;
                        const currentSymptoms = document.getElementById("current_symptoms").value;
                        const allergies = document.getElementById("allergies").value;
                        const medicalHistory = document.getElementById("medical_history").value;

                        return confirm(`以下為更新的資料，是否確認提交？\n病例號: ${medicalRecord}\n患者姓名: ${patientName}\n性別: ${gender}\n出生年月日: ${birthDate}\n當前症狀: ${currentSymptoms}\n過敏藥物: ${allergies}\n歷史重大疾病: ${medicalHistory}`);
                    }
                </script>


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
            </div>
        </div>
    </section>
    <?php mysqli_close($link); ?>
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