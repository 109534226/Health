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
                        <a href="留言頁面n.php?id=<?php echo htmlspecialchars($patient_id); ?>"
                            class="nav-item nav-link">留言</a>
                        <a href="n_Basicsee.php" class="nav-item nav-link">患者基本資訊</a>
                        <a href="n_recordssee.php" class="nav-item nav-link">病例歷史紀錄</a>
                        <a href="n_timesee.php" class="nav-item nav-link active">醫生的班表時段</a>
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
            <div class="d-flex align-items-center mb-5" style="text-align: right;">
                <h1 class="me-3 flex-shrink-0">醫生班表&gt;&gt;&gt;&gt;&gt;</h1>
                <div class="d-flex justify-content-end mb-5 w-100">
                    <a href="n_timesee.php" class="btn btn-primary" style="margin-left: 10px;">返回所有資料</a>
                </div>
            </div>
            <br />


            <?php
            include "db.php"; // 連接資料庫
            
            // 擷取搜尋的資料
            $搜尋詞 = isset($_POST['search']) ? $_POST['search'] : '';

            // 如果沒有輸入，顯示請輸入搜尋資料
            if (empty($搜尋詞)) {
                echo "<script>
    alert('請輸入搜尋資料');
    window.location.href = 'n_timesee.php';
    </script>";
                exit;
            }

            // 準備查詢
            $查詢語句 = "
    SELECT 
        ds.doctorshift_id AS id,
        ds.consultationD AS 日期,
        ds.clinicnumber_id AS 診間號,
        u.name AS 醫生姓名,
        d.department AS 科別,
        ds.created_at AS 紀錄創建時間,
        ds.consultationT_id AS 看診時段
    FROM doctorshift ds
    LEFT JOIN `user` u ON ds.user_id = u.user_id
    LEFT JOIN department d ON ds.medical_id = d.department_id
    WHERE u.name = ?
    ORDER BY ds.doctorshift_id ASC";

            $查詢準備 = $link->prepare($查詢語句);
            $查詢準備->bind_param("s", $搜尋詞); // 綁定參數
            $查詢準備->execute();
            $查詢結果 = $查詢準備->get_result();

            if (!$查詢結果) {
                die("查詢失敗: " . mysqli_error($link));
            }

            // 獲取總記錄數
            $總記錄數 = $查詢結果->num_rows;

            // 設定每頁顯示的記錄數
            $每頁記錄數 = 15;
            $總頁數 = ceil($總記錄數 / $每頁記錄數);

            // 獲取當前頁碼
            $當前頁碼 = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $當前頁碼 = max(1, min($總頁數, $當前頁碼)); // 確保當前頁碼在範圍內
            
            // 計算起始記錄
            $起始位置 = ($當前頁碼 - 1) * $每頁記錄數;

            // 查詢當前頁碼的資料
            $查詢語句 .= " LIMIT ?, ?";
            $查詢準備 = $link->prepare($查詢語句);
            $查詢準備->bind_param("sii", $搜尋詞, $起始位置, $每頁記錄數);
            $查詢準備->execute();
            $查詢結果 = $查詢準備->get_result();

            if (!$查詢結果) {
                die("查詢失敗: " . mysqli_error($link));
            }

            // 如果沒有找到任何記錄，顯示「查無此人資料」
            if ($總記錄數 == 0) {
                echo "<script>
    alert('查無此人');
    window.location.href = 'n_timesee.php';
    </script>";
                exit;
            }
            ?>

            <!-- 顯示資料 -->
            <div class="form-container">
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>看診日期</th>
                            <th>診間號</th>
                            <th>醫生姓名</th>
                            <th>看診時間</th>
                            <th>看診科別</th>
                            <th>紀錄創建時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($資料列 = mysqli_fetch_assoc($查詢結果)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($資料列['id']); ?></td>
                                <td><?php echo htmlspecialchars($資料列['日期']); ?></td>
                                <td><?php echo htmlspecialchars($資料列['診間號']); ?></td>
                                <td><?php echo htmlspecialchars($資料列['醫生姓名']); ?></td>
                                <td>
                                    <?php
                                    // 將看診時段的數字 ID 轉換為文字描述
                                    switch ($資料列['看診時段']) {
                                        case 1:
                                            echo '早';
                                            break;
                                        case 2:
                                            echo '午';
                                            break;
                                        case 3:
                                            echo '晚';
                                            break;
                                        default:
                                            echo '未知時段';
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($資料列['科別']); ?></td>
                                <td><?php echo htmlspecialchars($資料列['紀錄創建時間']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- 分頁 -->
            <!-- <div class="pagination">
                <?php for ($i = 1; $i <= $總頁數; $i++): ?>
                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div> -->


            <div class="pagination">
                <p>(總共 <?php echo $總記錄數; ?> 筆資料)</p> <!-- 顯示總資料筆數 -->

                <?php if ($當前頁碼 > 1): ?>
                    <a href="?page=<?php echo $當前頁碼 - 1; ?>">上一頁</a>
                <?php endif; ?>

                <span>第 <?php echo $當前頁碼; ?> 頁 / 共 <?php echo $總頁數; ?> 頁</span>

                <?php if ($當前頁碼 < $總頁數): ?>
                    <a href="?page=<?php echo $當前頁碼 + 1; ?>">下一頁</a>
                <?php endif; ?>
            </div>

            <style>
                table {
                    width: 100%;
                    /* 設定表格寬度為 100% */
                    border-collapse: collapse;
                    /* 將內框和外框線合併，避免雙線 */
                    border: 2px solid black;
                    /* 外框，設定表格外邊框線 */
                }

                th,
                td {
                    border: 1px solid black;
                    /* 內框，設定每個表格單元格之間的邊框 */
                    padding: 10px;
                    /* 單元格內部的間距 */
                    text-align: center;
                    /* 將文字置中 */
                }

                thead {
                    background-color: #f2f2f2;
                    /* 表頭背景顏色 */
                    font-weight: bold;
                    /* 設置表頭文字為粗體 */
                    font-size: 1.5em;
                    /* 設置表頭文字大小 */
                }

                tbody td {
                    font-size: 1.2em;
                    /* 設置表格內容文字大小 */
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