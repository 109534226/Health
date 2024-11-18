<?php
session_start();
include 'db.php'; // 引入資料庫連線文件

// 確認用戶已登入
if (!isset($_SESSION["登入狀態"]) || $_SESSION["登入狀態"] !== true) {
    echo "<script>
            alert('你還沒有登入，請先登入帳號。');
            window.location.href = 'login.php';
          </script>";
    exit();
}

// 確認是否存在要編輯的用戶ID
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>
            alert('無效的用戶ID。');
            window.location.href = 'c_user.php';
          </script>";
    exit();
}

// 從資料庫中查詢該用戶的資料
$sql = "SELECT * FROM user WHERE id=$id";
$result = mysqli_query($link, $sql);
$userData = mysqli_fetch_assoc($result);

if (!$userData) {
    echo "<script>
            alert('用戶不存在。');
            window.location.href = 'c_user.php';
          </script>";
    exit();
}

$用戶名 = $userData['username'] ?? '';
$電子郵件 = $userData['email'] ?? '';
$角色 = $userData['grade'] ?? '';
$狀態 = $userData['status'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $用戶名 = $_POST['username'];
    $電子郵件 = $_POST['email'];
    $角色 = $_POST['grade'];
    $狀態 = $_POST['status'];

    $updateSQL = "UPDATE user SET username='$用戶名', email='$電子郵件', grade='$角色', status='$狀態' WHERE id=$id";

    if (mysqli_query($link, $updateSQL)) {
        echo "<script>
                alert('更新成功');
                window.location.href = 'c_user.php';
              </script>";
    } else {
        echo "更新失敗：" . mysqli_error($link);
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html>

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


    <title>編輯用戶</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
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

    <?php include 'db.php'; ?> <!-- 引入頁首 -->

    <h1>編輯用戶</h1>
    <form method="POST" action="456.php">
        用戶名: <input type="text" name="username" value="<?php echo htmlspecialchars($用戶名); ?>" required><br>
        電子郵件: <input type="email" name="email" value="<?php echo htmlspecialchars($電子郵件); ?>" required><br>

        角色:
        <select name="grade">
            <option value="admin" <?php if ($角色 == "admin")
                echo "selected"; ?>>管理員</option>
            <option value="doctor" <?php if ($角色 == "doctor")
                echo "selected"; ?>>醫生</option>
            <option value="nurse" <?php if ($角色 == "nurse")
                echo "selected"; ?>>護士</option>
            <option value="user" <?php if ($角色 == "user")
                echo "selected"; ?>>使用者</option>
        </select><br>

        狀態:
        <select name="state">
            <option value="active" <?php if ($狀態 == "active")
                echo "selected"; ?>>活躍</option>
            <option value="inactive" <?php if ($狀態 == "inactive")
                echo "selected"; ?>>停用</option>
        </select><br>
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">


        <button type="submit">更新</button>
    </form>

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