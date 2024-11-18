<?php
session_start();
include "db.php";

// 確認是否登入
if (!isset($_SESSION["登入狀態"])) {
    header("Location: login.php");
    exit;
}

// 獲取使用者帳號
$帳號 = $_SESSION["帳號"];
$姓名 = $_SESSION["姓名"];
$電子郵件 = $_SESSION["電子郵件"];

// 查詢該帳號的使用者資料
$SQL檢查 = "SELECT * FROM people WHERE name = '$帳號'";
$result = mysqli_query($link, $SQL檢查);
$userData = mysqli_fetch_assoc($result);

// 將資料填入表單欄位
// $姓名 = $userData['username'] ?? '';
$出生年月日 = $userData['birthday'] ?? '';
$身分證字號 = $userData['idcard'] ?? '';
$電話 = $userData['phone'] ?? '';
// $電子郵件 = $userData['email'] ?? '';
// $緊急聯絡人 = $userData['ecname'] ?? '';
// $緊急聯絡人電話 = $userData['ecphone'] ?? '';

// 頭像設置
$profilePicture = !empty($userData['image'])
    ? 'data:image/jpeg;base64,' . base64_encode($userData['image'])
    : 'img/300.jpg';

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
            /* 讓图片完全覆蓋容器 */
            background-position: center;
            /* 確保圖片居中顯示 */
            background-repeat: no-repeat;
            /* 防止背景重複 */
            overflow: hidden;
            /* 隱藏藏溢出部分 */
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
                <a href="index.php" class="navbar-brand">
                    <h1 class="m-0 text-uppercase text-primary"><i class="fa fa-clinic-medical me-2"></i>健康醫療網站</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="留言介面d.php" class="nav-item nav-link"  value="<?php echo htmlspecialchars($patient_id); ?>">留言</a>
                        <a href="d_Basicsee.php" class="nav-item nav-link">患者基本資訊</a>
                        <a href="d_recordssee.php" class="nav-item nav-link">病例歷史紀錄</a>
                        <a href="d_timesee.php" class="nav-item nav-link">醫生的班表時段</a>
                        <a href="d_advicesee.php" class="nav-item nav-link">醫生建議</a>
                        <div class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="d_profile.php" class="dropdown-item active">關於我</a></li>
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

    <!-- 個人檔案表單 Start -->
    <div class="container-fluid">
        <div class="form-container">
            <form id="userForm" action="所有使用者資料2.php" method="post" enctype="multipart/form-data">

                <!-- 大頭貼上傳 -->
                <div class="form-row text-center">
                    <label for="fileInput">
                        <!-- 如果 $profilePicture 为空，則使用預設圖 img/300.jpg -->
                        <div class="profile-picture" id="profilePicturePreview"
                            style="background-image: url('<?php echo $profilePicture ? $profilePicture : 'img/300.jpg'; ?>');">
                        </div>
                        <button type="button" class="delete-avatar-button" onclick="deleteAvatar()">刪除頭像</button>
                    </label>
                    <input id="fileInput" type="file" name="profilePicture" accept="image/*"
                        onchange="uploadImage(event)">
                </div>

                <div class="form-row">
                    <label for="username">姓名 :</label>
                    <input id="username" type="text" name="username" value="<?php echo $姓名; ?>" disabled />
                </div>

                <div class="form-row">
                    <label for="userdate">出生年月日 :</label>
                    <input id="userdate" type="date" name="userdate" value="<?php echo $出生年月日; ?>"
                        max="<?php echo date('Y-m-d'); ?>" disabled>
                </div>

                <div class="form-row">
                    <label for="useridcard">身分證字號 :</label>
                    <input id="useridcard" type="text" name="useridcard" value="<?php echo $身分證字號; ?>"
                        placeholder="ex:A123456789" disabled>
                </div>

                <div class="form-row">
                    <label for="userphone">聯絡電話 :</label>
                    <input id="userphone" type="tel" name="userphone" value="<?php echo $電話; ?>"
                        placeholder="ex:0912345678" disabled>
                </div>

                <div class="form-row">
                    <label for="useremail">電子郵件 :</label>
                    <input id="useremail" type="email" name="useremail" value="<?php echo $電子郵件; ?>" disabled>
                </div>

                <!-- 操作按鈕 -->
                <div class="form-buttons">
                    <button type="button" id="editButton">修改資料</button>
                    <button type="button" id="confirmButton" style="display:none;" onclick="confirmData()">確認資料</button>
                </div>

            </form>
        </div>
    </div>

    <script>

        // 開啟欄位編輯功能
        document.getElementById('editButton').addEventListener('click', function () {
            document.querySelectorAll('input').forEach(function (input) {
                input.disabled = false;
            });
            document.getElementById('editButton').style.display = 'none'; // 隱藏“修改資料”按鈕
            document.getElementById('confirmButton').style.display = 'inline'; // 顯示“確認資料”按鈕
        });

        // 上傳並預覽圖片
        function uploadImage(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('profilePicturePreview').style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(file);

            // 使用 AJAX 上傳頭像
            const formData = new FormData();
            formData.append('profilePicture', file);

            fetch('頭像上傳.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 上傳成功後更新頁面上的頭像
                        document.getElementById('profilePicturePreview').src = data.imageUrl;
                    } else {
                        alert('頭像上传失败，请重试');
                    }
                })
                .catch(error => console.error('上传错误:', error));
        }



        function confirmData() {
            const username = document.getElementById('username').value.trim();
            const userdate = document.getElementById('userdate').value.trim();
            const useridcard = document.getElementById('useridcard').value.trim();
            const userphone = document.getElementById('userphone').value.trim();
            const useremail = document.getElementById('useremail').value.trim();

            // 驗證欄位格式

            // 姓名驗證: 空白檢查與格式檢查（只允許中文、英文和數字）
            if (!username) {
                alert('姓名欄位不能為空');
                return;
            } else if (!/^[\u4E00-\u9FA5a-zA-Z0-9]+$/.test(username)) {
                alert('姓名格式錯誤，只能包含中文、英文和數字，不能包含特殊符號');
                return;
            }

            // 出生年月日驗證: 空白檢查
            if (!userdate) {
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
            const identityNumber = document.getElementById("useridcard").value;  // 假設身分證欄位的ID是 useridcard
            if (!validateTaiwanID(identityNumber)) {
                return; // 若驗證失敗，則不繼續提交表單
            }



            // 聯絡電話驗證: 台灣手機號碼格式（09開頭，後面8位數字，且不允許後8位數出現6位或以上的重複數字）
            const phonePattern = /^09\d{8}$/;
            const repeatedPattern = /(\d)\1{5,}/; // 檢查是否有6個或更多相同的數字連續出現

            if (!userphone) {
                alert('聯絡電話欄位不能為空');
                return;
            } else if (!phonePattern.test(userphone)) {
                alert('聯絡電話格式錯誤，台灣手機號碼需為09開頭並有8位數字');
                return;
            } else if (repeatedPattern.test(userphone.slice(2))) {
                alert('聯絡電話格式錯誤，後面8位數字不可出現6位或以上重複的數字');
                return;
            }

            // 電子郵件驗證: 空白檢查與格式檢查
            if (!useremail) {
                alert('電子郵件欄位不能為空');
                return;
            } else if (!/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/.test(useremail)) {
                alert('電子郵件格式錯誤，請輸入有效的電子郵件');
                return;
            }

            const confirmMessage =
                `請確認您的資料:\n` +
                `姓名: ${username}\n` +
                `出生年月日: ${userdate}\n` +
                `身分證字號: ${useridcard}\n` +
                `聯絡電話: ${userphone}\n` +
                `電子郵件: ${useremail}\n` +
                `確定要提交資料嗎？`;

            // 顯示確認 alert
            if (confirm(confirmMessage)) {
                document.getElementById('userForm').action = '所有使用者資料2.php';
                document.getElementById('userForm').submit();
            }
        }




        // 刪除頭像並顯示預設圖片
        function deleteAvatar() {
            // 顯示確認視窗，要求使用者確認是否刪除頭像
            if (confirm("確定要刪除頭像嗎？")) {
                // 使用 fetch 發送 POST 請求至 `刪除頭像.php`
                fetch('刪除頭像.php', {
                    method: 'POST'
                })
                    .then(response => response.json()) // 解析回傳的 JSON 資料
                    .then(data => {
                        if (data.success) {
                            // 如果刪除成功，更新頁面上的頭像為預設圖片
                            document.getElementById('profilePicturePreview').style.backgroundImage = `url(${data.imageUrl})`;
                            // 顯示刪除成功的提示訊息
                            alert("頭像已成功刪除！");
                        } else {
                            alert("頭像刪除失敗，請重試"); // 刪除失敗的提示訊息
                        }
                    })
                    .catch(error => console.error("刪除錯誤:", error)); // 錯誤處理
            }
        }
    </script>
    <!-- 個人檔案表單 End -->

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