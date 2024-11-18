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
    <script>
    	// 用戶成功登入後，設置登錄狀態
    	sessionStorage.setItem('isLoggedIn', 'true');
	</script>
</head>

<body>
    <!-- 頁首 Start -->
    <div class="container-fluid sticky-top bg-white shadow-sm">
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
                    <a href="c_user.php" class="nav-item nav-link active">用戶管理</a>
                    <a href="c_content.php" class="nav-item nav-link ">內容管理</a> 
                    <a href="c_security.php" class="nav-item nav-link">安全管理</a>
              
                        <div class="nav-item">
                            <a href="c_profile.php" class="nav-link dropdown-toggle " data-bs-toggle="dropdown"
                                aria-expanded="false">個人檔案</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="c_profile.php" class="dropdown-item ">關於我</a></li>
                                <li><a href="c_change.php" class="dropdown-item">忘記密碼</a></li>
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

<?php
include 'db.php';

$sql = "SELECT * FROM user";
$result = mysqli_query($link, $sql);

if (!$result) {
    die("Query Failed: " . mysqli_error($link));
}

// 將所有查詢結果放入陣列中
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>用戶管理</title>
</head>
<body>
    <h1>用戶管理</h1>
    <a href="新增用戶.php">新增用戶</a>
    <table while="100%" border="1">
        <tr>
            <th>ID</th>
            <th>用戶名</th>
            <th>電子郵件</th>
            <th>角色</th>
            <th>狀態</th>
            <!-- <th>操作</th> -->
        </tr>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['grade']; ?></td>
                <!-- <td><?php echo $row['status']; ?></td> -->
                <td>
                    <!-- <a href="編輯用戶.php?id=<?php echo $row['id']; ?>">編輯</a> -->
                    <a href="刪除用戶.php?id=<?php echo $row['id']; ?>" onclick="return confirm('確定要刪除這位用戶嗎？');">刪除</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

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