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
                    <a href="留言頁面d.php?id=<?php echo htmlspecialchars($patient_id); ?>" class="nav-item nav-link">留言</a>
                    <a href="d_Basicsee.php" class="nav-item nav-link active">患者資料</a>
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
    </div>
    </nav>
</div>
</div>
<!-- 頁首 End -->



<div class="container-fluid"></div>
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
        <div class="d-flex align-items-center mb-5" style="text-align: right;">
            <h1 class="me-3 flex-shrink-0">患者資料&gt;&gt;&gt;&gt;&gt;</h1>
            <div class="d-flex justify-content-end mb-5 w-100">
                <a href="d_Basicsee.php" class="btn btn-primary" style="margin-left: 10px;">返回所有資料</a>
                <!-- <a href="d_Basic.php" class="btn btn-primary" style="margin-left: 10px;">填寫資料</a> -->
            </div>
        </div>

        <?php
        include "db.php"; // 連接資料庫
        

        // 擷取搜尋的資料，並去除多餘空白
        $搜尋詞 = isset($_POST['search']) ? trim($_POST['search']) : '';
        $_SESSION["搜尋詞"] = $搜尋詞;
        // echo "目前的搜尋詞: " . htmlspecialchars($_SESSION["搜尋詞"]);
        // $搜尋詞 =  $_SESSION["搜尋詞"];
        // echo $搜尋詞;
        // 如果表單有輸入，更新搜尋詞到 Session
        if (!empty(htmlspecialchars($_SESSION["搜尋詞"]))) {

        } elseif (empty($_SESSION["搜尋詞"])) {
            // 如果沒有輸入，且 Session 中也沒有搜尋詞，顯示警告並跳轉回 d_Basicsee.php
            echo "<script>
        alert('請輸入搜尋資料');
        window.location.href = 'd_Basicsee.php';
    </script>";
            exit;
        }

        // 計算分頁的起始記錄
        $當前頁碼 = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $每頁記錄數 = 15;
        $當前頁碼 = max(1, $當前頁碼);
        $起始位置 = ($當前頁碼 - 1) * $每頁記錄數;

        // 準備聯表查詢
        $查詢語句 = "
    SELECT 
        p.patient_id AS id,
        p.medicalnumber AS 病歷號,
        p.patientname AS 患者姓名,
        g.gender AS 性別,
        p.birthday AS 出生日期,
        p.currentsymptoms AS 當前狀況,
        p.allergies AS 過敏藥物,
        p.medicalhistory AS 歷史重大疾病,
        p.created_at AS 紀錄創建時間,
        ds.consultationD AS 看診日期,
        ct.consultationT AS 看診時段
    FROM patient p
    LEFT JOIN gender g ON p.gender_id = g.gender_id
    LEFT JOIN doctorshift ds ON p.doctorshift_id = ds.doctorshift_id
    LEFT JOIN consultationt ct ON ds.consultationT_id = ct.consultationT_id
    WHERE p.patientname = ?
    ORDER BY p.patient_id ASC
    LIMIT ?, ?";

        // 準備查詢並執行
        $查詢準備 = $link->prepare($查詢語句);
        $查詢準備->bind_param("sii", $搜尋詞, $起始位置, $每頁記錄數);
        $查詢準備->execute();
        $查詢結果 = $查詢準備->get_result();

        if (!$查詢結果) {
            die("查詢失敗: " . $link->error);
        }

        // 獲取總記錄數
        $總筆數查詢 = $link->prepare("SELECT COUNT(*) as 總數 FROM patient WHERE patientname = ?");
        $總筆數查詢->bind_param("s", $搜尋詞);
        $總筆數查詢->execute();
        $總筆數結果 = $總筆數查詢->get_result()->fetch_assoc();
        $總記錄數 = $總筆數結果['總數'];
        $總頁數 = ceil($總記錄數 / $每頁記錄數);

        // 如果沒有找到任何記錄，顯示「查無此人資料」
        if ($總記錄數 == 0) {
            echo "<script>
        alert('查無此人');
        window.location.href = 'd_Basicsee.php';
    </script>";
            exit;
        }
        ?>

        <div class="form-container">
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>病歷號</th>
                        <th>患者姓名</th>
                        <th>性別</th>
                        <th>出生日期</th>
                        <th>當前狀況</th>
                        <th>過敏藥物</th>
                        <th>歷史重大疾病</th>
                        <th>看診日期</th>
                        <th>看診時段</th>
                        <th>紀錄創建時間</th>
                        <th>功能選單</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($資料列 = $查詢結果->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($資料列['id']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['病歷號']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['患者姓名']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['性別']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['出生日期']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['當前狀況']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['過敏藥物']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['歷史重大疾病']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['看診日期']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['看診時段']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['紀錄創建時間']); ?></td>
                            <td>
                                <form action="患者資料修改000.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $資料列['id']; ?>">
                                    <button type="submit">修改</button>
                                </form>
                                <!-- <form method="POST" action="患者資料刪除nd.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $資料列['id']; ?>">
                                    <button type="submit" onclick="return confirm('確認要刪除這筆資料嗎？')">刪除</button>
                                </form> -->
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- 顯示分頁 -->
        <div class="pagination" style="text-align:center; margin-top: 20px;">
            <?php if ($總筆數 > 0 && $總頁數 > 1): ?>
                <?php if ($當前頁碼 > 1): ?>
                    <a href="?page=<?php echo $當前頁碼 - 1; ?>" style="margin-right: 10px;">上一頁</a>
                <?php endif; ?>

                <span>第 <?php echo $當前頁碼; ?> 頁 / 共 <?php echo $總頁數; ?> 頁</span>

                <?php if ($當前頁碼 < $總頁數): ?>
                    <a href="?page=<?php echo $當前頁碼 + 1; ?>" style="margin-left: 10px;">下一頁</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>



        <script>
            // 彈跳視窗
            function leaveMessage(patientId) {
                document.getElementById('patientId').value = patientId;
                document.getElementById('messageModal').style.display = "block";
            }

            function closeModal() {
                document.getElementById('messageModal').style.display = "none";
            }

            function editRow(id) {
                window.location.href = `患者資料修改000.php?id=${id}`;
            }
        </script>
        <style>
            /* 設置全域字體和背景 */
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                padding: 0;
            }

            /* 主容器樣式 */
            .form-container {
                max-width: 1200px;
                margin: 20px auto;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            /* 表格樣式 */
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }

            th {
                padding: 12px;
                text-align: center;
                border: 1px solid #dee2e6;
                background-color: #007bff;
                color: #ffffff;
                font-weight: bold;
                white-space: nowrap;
                /* 禁止換行 */
            }

            td {
                padding: 12px;
                text-align: center;
                border: 1px solid #dee2e6;
                white-space: nowrap;
                /* 禁止換行 */
            }

            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
        </style>



        <style>
            /* 頁碼 上一頁 下一頁 */
            .pagination {
                display: flex;
                flex-direction: column;
                /* 讓顯示的資料筆數與按鈕垂直排列 */
                justify-content: center;
                align-items: center;
                margin: 20px 0;
            }

            .pagination a {
                margin: 0 10px;
                text-decoration: none;
                color: #007BFF;
            }

            .pagination a:hover {
                text-decoration: underline;
            }

            .pagination span {
                margin: 0 10px;
            }

            .pagination p {
                margin-bottom: 10px;
                /* 與分頁按鈕之間留些距離 */
            }
        </style>
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