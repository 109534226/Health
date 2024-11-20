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
                        <a href="留言介面d.php" class="nav-item nav-link"
                            value="<?php echo htmlspecialchars($patient_id); ?>">留言</a>
                        <a href="d_Basicsee.php" class="nav-item nav-link">患者基本資訊</a>
                        <a href="d_recordssee.php" class="nav-item nav-link">病例歷史紀錄</a>
                        <a href="d_timesee.php" class="nav-item nav-link active">醫生的班表時段</a>
                        <a href="d_advicesee.php" class="nav-item nav-link">醫生建議</a>
                        <div class="nav-item">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="u_profile.php" class="dropdown-item">關於我</a></li>
                                <li><a href="u_change.php" class="dropdown-item">忘記密碼</a></li>
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
    include "db.php"; // 連接資料庫
    // 查詢登入使用者的身份（醫生或護士）
    $查詢角色 = "SELECT grade FROM user WHERE name = '$帳號'";
    $角色結果 = mysqli_query($link, $查詢角色);

    if ($角色結果 && $row = mysqli_fetch_assoc($角色結果)) {
        if ($row['grade'] == 1) {
            $_SESSION['user_role'] = '醫生';
        } elseif ($row['grade'] == 2) {
            $_SESSION['user_role'] = '護士';
        }
        echo "<script>console.log('角色設定為: " . $_SESSION['user_role'] . "');</script>"; // 調試訊息
    } else {
        // 加入更多錯誤資訊以協助調試
        echo "<script>alert('無法確定您的角色，請重新登入。'); console.error('角色查詢失敗或無此使用者');</script>";
        echo "<script>window.location.href = 'login.php';</script>";
        exit();
    }

    // 顯示當前角色
    echo "<p>當前角色: " . htmlspecialchars($_SESSION['user_role']) . "</p>";
    ?>

    <!--醫生班表-->
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
            <div class="d-flex align-items-center mb-5">
                <h1 class="me-3 flex-shrink-0">醫生班表&gt;&gt;&gt;&gt;&gt;</h1>
                <div class="input-group ms-auto" style="max-width: 550px;">
                    <form method="POST" action="d_timefind.php" class="d-flex w-100">
                        <input type="text" name="search" class="form-control p-3" placeholder="搜尋">
                        <button class="btn btn-primary px-3" type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div>
            </div>
            <br />

            <?php
            include "db.php"; // 連接資料庫
            
            // 擷取資料
            $查詢語句 = "SELECT * FROM doctorshift";
            $查詢結果 = mysqli_query($link, $查詢語句);

            if (!$查詢結果) {
                die("查詢失敗: " . mysqli_error($link));
            }

            // 獲取總記錄數
            $總記錄數查詢 = mysqli_query($link, "SELECT COUNT(*) as 總數 FROM doctorshift");
            if (!$總記錄數查詢) {
                die("查詢失敗: " . mysqli_error($link));
            }
            $總記錄數結果 = mysqli_fetch_assoc($總記錄數查詢);
            $總記錄數 = $總記錄數結果['總數'];

            // 設定每頁顯示的記錄數
            $每頁記錄數 = 15;
            $總頁數 = ceil($總記錄數 / $每頁記錄數);

            // 獲取當前頁碼
            $當前頁碼 = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $當前頁碼 = max(1, min($總頁數, $當前頁碼)); // 確保當前頁碼在範圍內
            
            // 計算起始記錄
            $起始位置 = ($當前頁碼 - 1) * $每頁記錄數;

            // 查詢當前頁碼的資料
            $查詢結果 = mysqli_query($link, "SELECT * FROM doctorshift LIMIT $起始位置, $每頁記錄數");
            if (!$查詢結果) {
                die("查詢失敗: " . mysqli_error($link));
            }
            ?>

            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>日期</th>
                        <th>診間號</th>
                        <th>醫生姓名</th>
                        <th>看診時間</th>
                        <th>紀錄創建時間</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($資料列 = mysqli_fetch_assoc($查詢結果)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($資料列['id']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['dateday']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['clinicnumber']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['doctorname']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['consultationperiod']); ?></td>
                            <td><?php echo htmlspecialchars($資料列['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

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


            <!-- 刪除 -->
            <!-- <th>功能選項</th> -->
            <!-- <td>
      <form method="POST" action="醫生班表刪除d.php" style="display:inline;">
      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
      <input type="hidden" name="source" value="n_advicefind">
     <button type="submit" onclick="return confirm('確認要刪除這筆資料嗎？')">刪除</button>
       </form>
      </td> -->
            <!-- <script>
        function deleteRow(id) {
            // 確認是否刪除
            if (confirm('確認要刪除這筆資料嗎？')) {
                alert('資料已刪除');
            } else {
                alert('取消刪除動作');
            }
        }
    </script> -->

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
            <a href="#" class="btn btn-lg btn-primary back-to-top"><i class="bi bi-arrow-up"></i></a>

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