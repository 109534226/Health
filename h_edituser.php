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
                        <a href="h_edituser.php" class="nav-item nav-link active">編輯用戶權限</a>
                        <a href="" class="nav-item nav-link">新增預約</a>
                        <a href="" class="nav-item nav-link">各科別報告</a>
                        <a href="" class="nav-item nav-link">滿意度分析</a>
                        <div class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="h_profile.php" class="dropdown-item">關於我</a></li>
                                <li><a href="h_change.php" class="dropdown-item">變更密碼</a></li>
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

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            font-family: Arial, sans-serif;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #FF9800;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        select {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        table,
        th,
        td {
            border: 1px solid #e0e0e0;
        }

        h1 {
            color: #007ACC;
            margin-bottom: 20px;
            /* text-align: center; */
        }
    </style>
    <!-- 顯示頁面標題 -->
    <h1>編輯使用者等級</h1>

    <?php
    // 啟用 Session，用來存儲和管理使用者的登入資訊
    session_start();

    // 從 Session 中獲取目前登入使用者的帳號
    $帳號 = $_SESSION["帳號"];

    // 從 Session 中獲取目前登入使用者的姓名，作為醫院名稱的參數
    $姓名 = $_SESSION['姓名']; // 醫院名稱直接等於姓名
    
    // 引入資料庫連線檔案，通常內含 $link 變數作為資料庫連線
    include 'db.php';

    // SQL 查詢字串，用於查詢與特定醫院相關的醫生、護士與一般使用者
    $sql = "
   SELECT 
       user.user_id,
       user.name AS username,
       user.account,
       grade.grade,
       grade.grade_id,
       COALESCE(department.department, '無') AS department,
       department.department_id
   FROM 
       user
   LEFT JOIN 
       grade ON user.grade_id = grade.grade_id
   LEFT JOIN 
       medical ON medical.user_id = user.user_id
   LEFT JOIN 
       department ON department.department_id = medical.department_id
   LEFT JOIN 
       hospital ON hospital.hospital_id = department.hospital_id
   WHERE 
       (hospital.hospital = '$姓名' AND (grade.grade = '醫生' OR grade.grade = '護士'))
       OR grade.grade = '使用者'
   ORDER BY 
       user.user_id ASC
";

    // 使用 mysqli_query 執行 SQL 查詢，並將結果存入 $result
    $result = mysqli_query($link, $sql);

    // 檢查 SQL 查詢是否執行成功
    if (!$result) {
        die("查詢失敗: " . mysqli_error($link));
    }

    // 使用 mysqli_fetch_all 將查詢結果轉換為關聯陣列，存入 $rows
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);

    // 查詢所有等級
    $grade_sql = "SELECT * FROM grade";
    $grade_result = mysqli_query($link, $grade_sql);
    $grades = mysqli_fetch_all($grade_result, MYSQLI_ASSOC);
    mysqli_free_result($grade_result);

    // 查詢特定醫院的科別
    $department_sql = "
    SELECT department.department, department.department_id
    FROM department
    LEFT JOIN hospital ON department.hospital_id = hospital.hospital_id
    WHERE hospital.hospital = '$姓名'
";
    $department_result = mysqli_query($link, $department_sql);
    $departments = mysqli_fetch_all($department_result, MYSQLI_ASSOC);
    mysqli_free_result($department_result);

    // 關閉資料庫連線
    mysqli_close($link);
    ?>

    <!-- HTML 顯示部分 -->
    <table>
        <thead>
            <tr style="background-color: orange;">
                <th>姓名</th> <!-- 表格標題，顯示使用者的姓名 -->
                <th>帳號</th> <!-- 表格標題，顯示使用者的帳號 -->
                <th>等級</th> <!-- 表格標題，顯示使用者的等級 -->
                <th>科別</th> <!-- 表格標題，顯示使用者的科別 -->
            </tr>
        </thead>
        <tbody id="user-table-body">
            <?php foreach ($rows as $row): ?> <!-- 循環遍歷每個使用者的資料，生成表格行 -->
                <tr data-user-id="<?php echo $row['user_id']; ?>"> <!-- 每個行包含一個使用者的資料，並設置使用者 ID 作為行的數據屬性 -->
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <!-- 顯示使用者的姓名，並使用 htmlspecialchars 防止 XSS 攻擊 -->
                    <td><?php echo htmlspecialchars($row['account']); ?></td>
                    <!-- 顯示使用者的帳號，並使用 htmlspecialchars 防止 XSS 攻擊 -->
                    <td>
                        <select class="grade-select" onchange="updateUser(this, '<?php echo $row['user_id']; ?>')">
                            <!-- 等級下拉選單，當選項變更時呼叫 updateUser 函數 -->
                            <option value="2" <?php echo ($row['grade'] == '醫生') ? 'selected' : ''; ?>>醫生</option>
                            <!-- 如果等級為醫生，則選中此選項 -->
                            <option value="3" <?php echo ($row['grade'] == '護士') ? 'selected' : ''; ?>>護士</option>
                            <!-- 如果等級為護士，則選中此選項 -->
                            <option value="1" <?php echo ($row['grade'] == '使用者') ? 'selected' : ''; ?>>使用者</option>
                            <!-- 如果等級為使用者，則選中此選項 -->
                        </select>
                    </td>
                    <td>
                        <select class="department-select" onchange="updateUser(this, '<?php echo $row['user_id']; ?>')">
                            <!-- 科別下拉選單，當選項變更時呼叫 updateUser 函數 -->
                            <option value="">無</option> <!-- 當科別為空時顯示 "無" -->
                            <?php foreach ($departments as $department): ?> <!-- 循環遍歷所有科別選項 -->
                                <option value="<?php echo htmlspecialchars($department['department_id']); ?>" 
                                <?php echo ($department['department_id'] == $row['department_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($department['department']); ?> <!-- 顯示科別名稱，並使用 htmlspecialchars 防止 XSS 攻擊 -->
                                </option>
                                <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script>
// 更新使用者資料的 JavaScript 函數
function updateUser(selectElement, userId) {
    const selectedValue = selectElement.value; // 獲取選擇的值
    const fieldName = selectElement.classList.contains('grade-select') ? 'grade_id' : 'department_id'; // 根據選擇元素的類別來確定要更新的字段

    const xhr = new XMLHttpRequest(); // 建立一個新的 XMLHttpRequest 對象
    xhr.open("POST", "醫院編輯.php", true); // 設定請求為 POST 方法，並指定目標為 update_user.php
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // 設定請求的內容類型為表單資料
    xhr.onreadystatechange = function () { // 註冊一個回調函數來處理請求的狀態改變
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                alert(xhr.responseText); // 顯示來自伺服器的訊息
            } else {
                console.error("更新失敗: ", xhr.responseText); // 顯示錯誤信息
                alert("更新失敗，請檢查控制台以獲取更多信息");
            }
        }
    };
    xhr.send("user_id=" + userId + "&field=" + fieldName + "&value=" + selectedValue); // 發送請求，包含使用者 ID、欄位名稱和更新的值
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