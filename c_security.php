<?php
session_start(); // 啟動 PHP Session，確保可以使用 $_SESSION 變數。

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

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 10px;
      text-align: left;
      border: 1px solid #ddd;
    }

    th {
      background-color: #4CAF50;
      color: white;
    }
  </style>
  <script>
    // 用戶成功登入後，設置登錄狀態
    sessionStorage.setItem('isLoggedIn', 'true');
  </script>
</head>

<body>
  <!-- 頁首區塊開始 -->
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
            <a href="c_user.php" class="nav-item nav-link">用戶管理</a>
            <a href="c_content.php" class="nav-item nav-link">內容管理</a>
            <a href="c_security.php" class="nav-item nav-link active">安全管理</a>

            <!-- 個人檔案下拉選單 -->
            <div class="nav-item">
              <a href="#" class="nav-link dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false">個人檔案</a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a href="c_profile.php" class="dropdown-item">關於我</a></li>
                <li><a href="c_change.php" class="dropdown-item">忘記密碼</a></li>
                <li><a href="#" class="dropdown-item" onclick="showLogoutBox()">登出</a></li>
                <li><a href="#" class="dropdown-item" onclick="showDeleteAccountBox()">刪除帳號</a></li>

                <!-- 隱藏的表單，用於提交刪除帳號請求 -->
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
  <!-- 頁首區塊結束 -->


  <!-- <h2>安全管理系統</h2>

  <table>
    <thead>
      <tr>
        <th>使用者類型</th>
        <th>帳號</th>
        <th>登入時間</th>
      </tr>
    </thead>
    <tbody id="userTable">
      使用JavaScript動態新增資料
    </tbody>
  </table> -->

  <script>
    // // 模擬一些登入的使用者數據，包括帳號
    // const users = [
    //   { type: "醫生", account: "doctor123", loginTime: "2024-11-14 08:15:00" },
    //   { type: "護士", account: "nurse456", loginTime: "2024-11-14 09:05:00" },
    //   { type: "管理者", account: "admin789", loginTime: "2024-11-14 10:25:00" },
    //   { type: "醫生", account: "doctor999", loginTime: "2024-11-14 11:30:00" }
    // ];

    // // 動態生成表格內容   
    // const userTable = document.getElementById("userTable");
    // users.forEach(user => {
    //   const row = document.createElement("tr");
    //   const userTypeCell = document.createElement("td");
    //   const accountCell = document.createElement("td");
    //   const loginTimeCell = document.createElement("td");
    // });

    // JavaScript 功能函數
    // 顯示登出彈出框
    function showLogoutBox() {
      document.getElementById('logoutBox').style.display = 'flex';
    }

    // 隱藏登出彈出框
    function hideLogoutBox() {
      document.getElementById('logoutBox').style.display = 'none';
    }

    // 執行登出操作
    function logout() {
      alert('你已經登出！');
      hideLogoutBox();
      window.location.href = 'login.php'; // 替換為登出後的頁面
    }

    // 顯示刪除帳號彈出框
    function showDeleteAccountBox() {
      document.getElementById('deleteAccountBox').style.display = 'flex';
    }

    // 隱藏刪除帳號彈出框
    function hideDeleteAccountBox() {
      document.getElementById('deleteAccountBox').style.display = 'none';
    }

    // 提交刪除帳號表單
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