<?php
session_start();
include "db.php";

// 確認是否登入
if (!isset($_SESSION["登入狀態"])) {
    header("Location: login.html");
    exit;
}

// 獲取使用者帳號
$帳號 = $_SESSION["帳號"];

// 防止頁面被瀏覽器緩存
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

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

        /* 表單樣式調整 */
        .form-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            color: #00695c;
            /* 使用醫療相關的深綠色 */
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            color: #004d40;
            /* 深綠色以突出標籤文字 */
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #80cbc4;
            /* 使用健康綠色作為邊框顏色 */
            background-color: #e0f2f1;
            /* 淺綠色背景，讓輸入框顯得舒適 */
            padding: 10px;
        }

        .form-control:focus {
            border-color: #004d40;
            /* 在聚焦時強調邊框顏色 */
            box-shadow: 0 0 8px rgba(0, 77, 64, 0.4);
            /* 聚焦效果，增加陰影 */
        }

        .btn-primary {
            background-color: #00796b;
            border-color: #00796b;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #004d40;
            border-color: #004d40;
        }

        .btn-secondary {
            background-color: #b2dfdb;
            border-color: #b2dfdb;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
        }

        .btn-secondary:hover {
            background-color: #80cbc4;
            border-color: #80cbc4;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
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
                        <a href="u_index.php" class="nav-item nav-link">首頁</a>
                        <a href="u_medical.php" class="nav-item nav-link">相關醫療資訊</a>
                        <a href="u_map.php" class="nav-item nav-link active">預約及現場掛號人數</a>
                        <a href="u_story.php" class="nav-item nav-link">患者故事與經驗分享</a>
                        <div class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
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
    <div class="container mt-5">
        <div class="card p-4">
            <div class="form-title">預約資料填寫</div>
            <div class="form-group">
                <?php
                // 假設從之前的表單中已經通過 POST 方法接收到這些值
                $date = isset($_POST['date']) ? htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8') : '';
                $doctorName = isset($_POST['doctor']) ? htmlspecialchars($_POST['doctor'], ENT_QUOTES, 'UTF-8') : '';
                $registeredCount = isset($_POST['registeredCount']) ? htmlspecialchars($_POST['registeredCount'], ENT_QUOTES, 'UTF-8') : '';
                $timePeriod = isset($_POST['timePeriod']) ? htmlspecialchars($_POST['timePeriod'], ENT_QUOTES, 'UTF-8') : '';
                $selectedClinic = isset($_POST['clinic']) ? htmlspecialchars($_POST['clinic'], ENT_QUOTES, 'UTF-8') : '';
                $selectedDepartment = isset($_POST['department']) ? htmlspecialchars($_POST['department'], ENT_QUOTES, 'UTF-8') : '';

                // if (empty($selectedClinic) || empty($selectedDepartment)) {
                //     echo "<script>alert('醫療診所或科別的值沒有正確傳遞。請重新選擇！');</script>";
                // } else {
                //     echo "<p>診所: $selectedClinic</p>";
                //     echo "<p>科別: $selectedDepartment</p>";
                // }
                ?>

                <form action="預約.php" method="POST">

                    <!-- 顯示用的醫療診所欄位 -->
                    <div class="form-group">
                        <label for="hospital_display">醫療診所</label>
                        <input type="text" class="form-control" id="hospital_display"
                            value="<?php echo $selectedClinic; ?>" disabled>
                        <input type="hidden" name="hospital" id="hospital" value="<?php echo $selectedClinic; ?>">
                    </div>

                    <!-- 顯示用的科別欄位 -->
                    <div class="form-group">
                        <label for="department_display">科別</label>
                        <input type="text" class="form-control" id="department_display"
                            value="<?php echo $selectedDepartment; ?>" disabled>
                        <input type="hidden" name="department" id="department"
                            value="<?php echo $selectedDepartment; ?>">
                    </div>


                    <!-- 看診時段欄位，顯示用的禁用框加上隱藏字段 -->
                    <div class="form-group">
                        <label for="consultationtime">看診時段</label>
                        <input type="text" class="form-control" id="consultationtime_display"
                            value="<?php echo $timePeriod; ?>" disabled>
                        <input type="hidden" name="consultationtime" id="consultationtime" value="<?php echo $timePeriod; ?>">
                    </div>

                    <!-- 看診日期欄位，顯示用的禁用框加上隱藏字段 -->
                    <div class="form-group">
                        <label for="date">看診日期</label>
                        <input type="text" class="form-control" id="date_display" value="<?php echo $date; ?>" disabled>
                        <input type="hidden" name="date" id="date" value="<?php echo $date; ?>">
                    </div>


                    <!-- 醫生欄位，預設為傳遞過來的醫生名稱 -->
                    <div class="form-group">
                        <label for="doctor">醫生</label>
                        <input type="text" class="form-control" id="doctor" name="doctor"
                            value="<?php echo $doctorName; ?>" disabled>
                    </div>

                    <!-- 目前預約人數欄位，預設為傳遞過來的目前預約人數 -->
                    <div class="form-group">
                        <label for="registeredCount">目前預約人數</label>
                        <input type="text" class="form-control" id="registeredCount" name="registeredCount"
                            value="<?php echo $registeredCount; ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="patientname">姓名</label>
                        <input type="text" class="form-control" id="patientname" name="patientname" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">性別</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">請選擇性別</option>
                            <option value="男">男</option>
                            <option value="女">女</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="idcard">身分證字號</label>
                        <input type="text" class="form-control" id="idcard" name="idcard" required>
                    </div>
                    <div class="form-group">
                        <label for="birthday">出生年月日</label>
                        <input type="date" class="form-control" id="birthday" name="birthday" required
                            max="<?php echo date('Y-m-d'); ?>"
                            min="<?php echo date('Y-m-d', strtotime('-120 years')); ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">行動電話</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="address">地址</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="current">目前症狀</label>
                        <textarea class="form-control" id="current" name="current" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="allergies">過敏藥物</label>
                        <textarea class="form-control" id="allergies" name="allergies" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="medicalhistory">歷史重大疾病</label>
                        <textarea class="form-control" id="medicalhistory" name="medicalhistory" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="confirmData()">確認送出</button>
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">返回</button>
                </form>
            </div>
        </div>
        <script>
            // 確認資料並顯示 alert
            function confirmData() {
                const patientname = document.getElementById('patientname').value.trim();
                const gender = document.getElementById('gender').value.trim();
                const birthday = document.getElementById('birthday').value.trim();
                const idcard = document.getElementById('idcard').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const address = document.getElementById('address').value.trim();
                const allergies = document.getElementById('allergies').value.trim();
                const medicalhistory = document.getElementById('medicalhistory').value.trim();

                const selectedClinic = document.getElementById('hospital').value.trim();
                const selectedDepartment = document.getElementById('department').value.trim();
                const consultationTime = document.getElementById('consultationtime').value.trim();
                const date = document.getElementById('date').value.trim();
                const doctorName = document.getElementById('doctor').value.trim();
                const registeredCount = document.getElementById('registeredCount').value.trim();


                // 檢查所有欄位是否為空
                if (!patientname) {
                    alert('姓名欄位不能為空');
                    return;
                }
                if (!birthday) {
                    alert('出生年月日欄位不能為空');
                    return;
                }
                if (!gender) {
                    alert('性別欄位不能為空');
                    return;
                }
                if (!idcard) {
                    alert('身分證字號欄位不能為空');
                    return;
                }
                if (!phone) {
                    alert('行動電話欄位不能為空');
                    return;
                }
                if (!address) {
                    alert('地址欄位不能為空');
                    return;
                }
                if (!allergies) {
                    alert('過敏藥物欄位不能為空');
                    return;
                }
                if (!medicalhistory) {
                    alert('歷史重大疾病欄位不能為空');
                    return;
                }
                // 驗證欄位格式

                // 姓名驗證: 空白檢查與格式檢查（只允許中文、英文和數字）
                if (!patientname) {
                    alert('姓名欄位不能為空');
                    return;
                } else if (!/^[\u4E00-\u9FA5a-zA-Z0-9]+$/.test(patientname)) {
                    alert('姓名格式錯誤，只能包含中文、英文和數字，不能包含特殊符號');
                    return;
                }

                // 出生年月日驗證: 空白檢查
                if (!birthday) {
                    alert('出生年月日欄位不能為空');
                    return;
                }

                // 台灣身分證字號驗證函數
                function validateTaiwanID(identityNumber) {
                    // 檢查身分證字號是否符合正則格式
                    const identityFormat = /^[A-Z][1-2]\d{8}$/;
                    if (!identityFormat.test(identityNumber)) {
                        alert("身分證字號格式錯誤，請確認格式是否正確！");
                        return false;
                    }

                    // 首字母對應的數字範圍
                    const letterToNumberMap = {
                        "A": 10, "B": 11, "C": 12, "D": 13, "E": 14, "F": 15, "G": 16,
                        "H": 17, "J": 18, "K": 19, "L": 20, "M": 21, "N": 22, "P": 23,
                        "Q": 24, "R": 25, "S": 26, "T": 27, "U": 28, "V": 29, "X": 30,
                        "W": 31, "Y": 32, "Z": 33, "I": 34, "O": 35
                    };

                    // 取得字母部分
                    const firstLetter = identityNumber[0];

                    // 檢查字母是否合法
                    if (!letterToNumberMap.hasOwnProperty(firstLetter)) {
                        alert("身分證字號的首字母無效！");
                        return false;
                    }

                    // 轉換字母為數字
                    const firstLetterNumber = letterToNumberMap[firstLetter];

                    // 拆解身分證字號，取得每個數字
                    const digits = identityNumber.slice(1).split("").map(Number);

                    // 以身分證字號的第一個字母轉換成兩位數，並與後續數字結合
                    const firstDigit = Math.floor(firstLetterNumber / 10); // 取得字母數字的十位數
                    const secondDigit = firstLetterNumber % 10;           // 取得字母數字的個位數

                    // 將所有數字組成一個陣列
                    const fullDigits = [firstDigit, secondDigit, ...digits];

                    // 計算加權總和：每一位數字與對應的權重值相乘，然後求和
                    const weights = [1, 9, 8, 7, 6, 5, 4, 3, 2, 1, 1];  // 權重值
                    let weightedSum = 0;
                    for (let i = 0; i < fullDigits.length; i++) {
                        // console.log(fullDigits[i]);

                        // // 顯示在提示框中
                        // alert(fullDigits[i]);
                        weightedSum += fullDigits[i] * weights[i];
                    }
                    // console.log(weightedSum);

                    // // 顯示在提示框中
                    // alert(weightedSum);

                    // 檢查加權總和是否能被 10 整除
                    if (weightedSum % 10 !== 0) {
                        alert("身分證字號無效，請確認輸入的字號！");
                        return false;
                    }

                    // 如果檢查通過，返回 true
                    return true;
                }

                // 用法範例
                const identityNumber = document.getElementById("idcard").value;  // 假設身分證欄位的ID是 idcard
                if (!validateTaiwanID(identityNumber)) {
                    return; // 若驗證失敗，則不繼續提交表單
                }


                // 聯絡電話驗證: 台灣手機號碼格式（09開頭，後面8位數字，且不允許後8位數出現6位或以上的重複數字）
                const phonePattern = /^09\d{8}$/;
                const repeatedPattern = /(\d)\1{5,}/; // 檢查是否有6個或更多相同的數字連續出現

                if (!phone) {
                    alert('行動電話欄位不能為空');
                    return;
                } else if (!phonePattern.test(phone)) {
                    alert('行動電話格式錯誤，台灣手機號碼需為09開頭並有8位數字');
                    return;
                } else if (repeatedPattern.test(phone.slice(2))) {
                    alert('行動電話格式錯誤，後面8位數字不可出現6位或以上重複的數字');
                    return;
                }

                // 組合要顯示在 alert 的訊息
                const confirmMessage =
                    `請確認您的資料:\n` +
                    `診所: ${selectedClinic}\n` +
                    `科別: ${selectedDepartment}\n` +
                    `看診時段: ${consultationTime}\n` +
                    `看診日期: ${date}\n` +
                    `醫生: ${doctorName}\n` +
                    `目前預約人數: ${registeredCount}\n` +
                    `姓名: ${patientname}\n` +
                    `性別: ${gender}\n` +
                    `出生年月日: ${birthday}\n` +
                    `身分證字號: ${idcard}\n` +
                    `行動電話: ${phone}\n` +
                    `地址: ${address}\n` +
                    `過敏藥物: ${allergies}\n` +
                    `歷史重大疾病: ${medicalhistory}\n` +
                    `確定要提交資料嗎？`;

                // 顯示確認 alert
                if (confirm(confirmMessage)) {
                    document.querySelector('form').submit(); // 確認後提交表單
                }
            }
        </script>


        <!-- 回到頁首(Top 箭頭 -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


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