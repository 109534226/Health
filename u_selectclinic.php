<?php
session_start();
// 引入資料庫連線
include('db.php');

// 檢查是否登入
if (!isset($_SESSION["登入狀態"])) {
    header("Location: login.html");
    exit;
}

// 防止頁面被瀏覽器快取
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

// 清除輸出緩衝區
ob_clean();
flush();

// 檢查帳號和姓名是否存在於 $_SESSION 中
if (isset($_SESSION["帳號"]) && isset($_SESSION["姓名"])) {
    // 獲取使用者帳號和姓名
    $account = $_SESSION['帳號'];
    $name = $_SESSION['姓名'];
} else {
    echo "<script>
            alert('會話過期或資料遺失，請重新登入。');
            window.location.href = 'login.html';
          </script>";
    exit();
}

include 'db.php'; // 引入資料庫連接

// 從前一個表單接收診所和科別的值
$selectedClinic = isset($_POST['clinic']) ? mysqli_real_escape_string($link, $_POST['clinic']) : null;
$selectedDepartment = isset($_POST['department']) ? mysqli_real_escape_string($link, $_POST['department']) : null;

// 檢查診所和科別是否都已提供
if ($selectedClinic && $selectedDepartment) {
    // 獲取目前的日期
    $currentDate = date('Y-m-d');

    $query = "
        SELECT ds.*, ct.consultationT, cn.clinicnumber, u.name AS doctorname
        FROM doctorshift ds
        JOIN consultationt ct ON ds.consultationT_id = ct.consultationT_id
        JOIN clinicnumber cn ON ds.clinicnumber_id = cn.clinicnumber_id
        JOIN medical m ON ds.medical_id = m.medical_id
        JOIN department d ON m.department_id = d.department_id
        JOIN hospital h ON d.hospital_id = h.hospital_id
        JOIN user u ON m.user_id = u.user_id
        WHERE ds.consultationD >= '$currentDate'
        AND h.hospital = '$selectedClinic'
        AND d.department = '$selectedDepartment'
        ORDER BY ds.consultationD ASC
    ";

    $result = mysqli_query($link, $query);

    // 初始化陣列來儲存早、午、晚班的資料
    $morningShifts = [];
    $afternoonShifts = [];
    $eveningShifts = [];

    // 根據不同的班次來分類資料
    while ($row = mysqli_fetch_assoc($result)) {
        switch ($row['consultationT']) {
            case '早':
                $morningShifts[] = $row;
                break;
            case '午':
                $afternoonShifts[] = $row;
                break;
            case '晚':
                $eveningShifts[] = $row;
                break;
        }
    }

    // 顯示排班資訊的函數
    function displayShifts($shifts, $timePeriod)
    {
        global $selectedClinic, $selectedDepartment; // 使用全局变量，以便表单生成时可访问这些值

        echo "<div class='shift-section'>";
        echo "<h2>$timePeriod:</h2>";
        echo "<table class='table table-bordered'>";
        echo "<thead><tr class='bg-success text-white'><th style='text-align: center;'>診間號</th>";
        for ($i = 0; $i < 7; $i++) {
            // 顯示表格標頭，包含星期幾和日期
            $dayOffset = "+$i day";
            $date = date('Y-m-d', strtotime($dayOffset));
            $dayName = date('l', strtotime($dayOffset));
            $weekDaysCn = [
                'Monday' => '星期一',
                'Tuesday' => '星期二',
                'Wednesday' => '星期三',
                'Thursday' => '星期四',
                'Friday' => '星期五',
                'Saturday' => '星期六',
                'Sunday' => '星期日'
            ];
            $dayNameCn = $weekDaysCn[$dayName];
            echo "<th style='text-align: center;'>$dayNameCn<br>" . date('m-d', strtotime($date)) . "</th>";
        }
        echo "</tr></thead>";

        // 顯示每一天的班次資訊
        echo "<tbody>";
        $clinicNumbers = array_unique(array_column($shifts, 'clinicnumber'));
        foreach ($clinicNumbers as $clinicNumber) {
            echo "<tr>";
            echo "<td style='color: red; font-weight: bold; text-align: center;'>$clinicNumber</td>";
            for ($i = 0; $i < 7; $i++) {
                $dayOffset = "+$i day";
                $date = date('Y-m-d', strtotime($dayOffset));
                $hasShift = false;
                foreach ($shifts as $shift) {
                    if ($shift['clinicnumber'] == $clinicNumber && $shift['consultationD'] == $date) {
                        $doctorName = $shift['doctorname'];
                        $registeredCount = $shift['reserve'] ?? 0; // 預設為 0，如果為空則顯示 0

                        // 顯示每個班次的資訊
echo "<td style='font-weight: bold; text-align: center;'>
$doctorName<br>目前預約人數: $registeredCount
<form action='u_registration.php' method='POST' style='margin-top: 10px;'>

    <!-- 診所名稱，從之前的表單中接收的值 -->
    <input type='hidden' name='clinic' value='" . htmlspecialchars($selectedClinic, ENT_QUOTES, 'UTF-8') . "'>

    <!-- 科別名稱，從之前的表單中接收的值 -->
    <input type='hidden' name='department' value='" . htmlspecialchars($selectedDepartment, ENT_QUOTES, 'UTF-8') . "'>

    <!-- 診間號，從班次資料中獲取的值 -->
    <input type='hidden' name='clinicnumber' value='" . htmlspecialchars($clinicNumber, ENT_QUOTES, 'UTF-8') . "'>

    <!-- 看診日期，從班次資料中獲取的值（對應當天日期） -->
    <input type='hidden' name='date' value='" . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . "'>

    <!-- 醫生姓名，從班次資料中獲取的值 -->
    <input type='hidden' name='doctor' value='" . htmlspecialchars($doctorName, ENT_QUOTES, 'UTF-8') . "'>

    <!-- 目前預約人數，從班次資料中獲取的值（預設為 0） -->
    <input type='hidden' name='registeredCount' value='" . htmlspecialchars($registeredCount, ENT_QUOTES, 'UTF-8') . "'>

    <!-- 看診時段（早/午/晚），根據不同班次顯示的時段 -->
    <input type='hidden' name='timePeriod' value='" . htmlspecialchars($timePeriod, ENT_QUOTES, 'UTF-8') . "'>

    <button type='submit' class='btn btn-primary btn-sm'>我要預約</button>
</form>
</td>";


                        $hasShift = true;
                        break;
                    }
                }
                if (!$hasShift) {
                    echo "<td style='font-weight: bold; text-align: center;'></td>";
                }
            }
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }

    // 顯示早班、午班、晚班的資訊
    // displayShifts($morningShifts, '上午診');
    // displayShifts($afternoonShifts, '下午診');
    // displayShifts($eveningShifts, '晚上診');

} else {
    echo "請選擇診所和看診科別。";
}

mysqli_close($link);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>健康醫療網站</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
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
        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }

        .table th {
            color: white;
        }

        .table td {
            color: black;
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
                <a href="u_index.php" class="navbar-brand">
                    <h1 class="m-0 text-uppercase text-primary"><i class="fa fa-clinic-medical me-2"></i>健康醫療網站</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="u_index.php" class="nav-item nav-link">首頁</a>
                        <a href="u_medical.php" class="nav-item nav-link">相關醫療資訊</a>
                        <a href="u_map.php" class="nav-item nav-link  active">預約及現場掛號人數</a>
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
                                <form id="deleteAccountForm" action="刪除.php" method="POST" style="display:none;">
                                    <input type="hidden" name="user_id" value="12345"> <!-- 用戶ID，從後端獲取 -->
                                </form>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- 頁首 End -->

    <!-- 登出對話框 Start -->
    <div id="logoutBox" class="logout-box">
        <div class="logout-dialog">
            <p>你確定要登出嗎？</p>
            <button onclick="logout()">確定</button>
            <button onclick="hideLogoutBox()">取消</button>
        </div>
    </div>
    <!-- 登出對話框 End -->

    <!-- 回到頁首(Top 箭頭 -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

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
            window.location.href = 'index.html'; // 替換為登出後的頁面
        }
    </script>

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
    <!-- 返回按鈕 Start -->
    <div class="container mb-5">
        <button id="backButton" class="btn btn-success btn-lg">返回</button>
    </div>
    <!-- 返回按鈕 End -->

    <script>
        document.getElementById('backButton').addEventListener('click', function () {
            // 使用瀏覽器的返回功能
            window.history.back();
        });
    </script>

    <main>
        <div class="container">
            <?php

            // 顯示診所和科別的值
            echo "<p>選擇的診所: " . ($selectedClinic ? htmlspecialchars($selectedClinic, ENT_QUOTES, 'UTF-8') : "未選擇") . "</p>";
            echo "<p>選擇的科別: " . ($selectedDepartment ? htmlspecialchars($selectedDepartment, ENT_QUOTES, 'UTF-8') : "未選擇") . "</p></br>";


            // 顯示各個時段的排班
            displayShifts($morningShifts, '上午診');
            displayShifts($afternoonShifts, '下午診');
            displayShifts($eveningShifts, '晚上診');
            ?>
        </div>
    </main>

    <!-- "我要預約" 按鈕 Start -->
    <!-- <div class="container text-center mb-5">
        <button id="reserveButton" class="btn btn-primary btn-lg">我要預約</button>
    </div> -->
    <!-- "我要預約" 按鈕 End -->

    <!-- <script>
        document.getElementById('reserveButton').addEventListener('click', function () {
            // 點擊“我要預約”按鈕後跳轉至預約頁面，並傳遞科別和醫院資訊
            let hospital = "<?php echo $row['hospital']; ?>";
            let department = "<?php echo $row['department']; ?>";
            window.location.href = 'u_registration.php?hospital=' + encodeURIComponent(hospital) + '&department=' + encodeURIComponent(department);
        });
    </script> -->

    <!-- 頁尾 Start -->
    <div class="container-fluid bg-dark text-light mt-5 py-5">
        <div class="container py-5">
            <div class="row g-5">
                <!-- 快速連結 -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
                        快速連結</h4>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-light mb-2" href="u_index.php"><i class="fa fa-angle-right me-2"></i>首頁</a>
                        <a class="text-light mb-2" href="u_medical.php"><i class="fa fa-angle-right me-2"></i>相關醫療資訊</a>
                        <a class="text-light mb-2" href="u_map.php"><i class="fa fa-angle-right me-2"></i>預約及現場掛號人數</a>
                        <a class="text-light mb-2" href="u_story.php"><i
                                class="fa fa-angle-right me-2"></i>患者故事與經驗分享</a>
                        <a class="text-light mb-2" href="u_profile.php"><i class="fa fa-angle-right me-2"></i>關於我</a>
                    </div>
                </div>
                <!-- 評分 -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
                        評分</h4>
                    <div class="d-flex flex-column justify-content-start">
                        <div id="rating">
                            <ul id="star-rating" style="list-style-type:none; padding-left:0;">
                                <li style="display:inline-block; cursor:pointer;" data-value="1"><i
                                        class="fa fa-star"></i></li>
                                <li style="display:inline-block; cursor:pointer;" data-value="2"><i
                                        class="fa fa-star"></i></li>
                                <li style="display:inline-block; cursor:pointer;" data-value="3"><i
                                        class="fa fa-star"></i></li>
                                <li style="display:inline-block; cursor:pointer;" data-value="4"><i
                                        class="fa fa-star"></i></li>
                                <li style="display:inline-block; cursor:pointer;" data-value="5"><i
                                        class="fa fa-star"></i></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <style>
                    /* 預設的星星顏色 */
                    #star-rating .fa-star {
                        color: #ccc;
                    }

                    /* 選中的星星顏色 */
                    #star-rating .fa-star.selected {
                        color: #f39c12;
                    }

                    /* 滑過時星星變色 */
                    #star-rating .fa-star:hover,
                    #star-rating .fa-star.hover {
                        color: #f39c12;
                    }
                </style>

                <script>
                    // 取得所有星星的元素
                    const stars = document.querySelectorAll('#star-rating .fa-star');

                    // 將所有星星綁定點擊事件
                    stars.forEach((star, index) => {
                        star.addEventListener('click', function () {
                            const ratingValue = index + 1; // 获取评分值

                            // 移除所有星星的 selected 類別
                            stars.forEach(s => s.classList.remove('selected'));
                            // 為點選的星星和之前的星星加上 selected 類別
                            for (let i = 0; i <= index; i++) {
                                stars[i].classList.add('selected');
                            }

                            // 更新星星樣式
                            document.querySelectorAll('#star-rating li').forEach(function (star) {
                                if (star.getAttribute('data-value') <= ratingValue) {
                                    star.classList.add('text-warning');
                                } else {
                                    star.classList.remove('text-warning');
                                }
                            });

                            // 使用 AJAX 发送评分数据
                            const xhr = new XMLHttpRequest();
                            xhr.open("POST", "評分.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhr.onreadystatechange = function () {
                                if (xhr.readyState == 4 && xhr.status == 200) {
                                    // 显示成功提示
                                    swal("感謝您的評分！", "您給了 " + ratingValue + " 星！", "success");
                                }
                            };
                            xhr.send("score=" + ratingValue);

                            // 彈出評分提示
                            swal("感謝您的評分！", "您給了 " + ratingValue + " 星！", "success");
                        });

                        // 滑鼠移到星星上時，顯示即時的 hover 效果
                        star.addEventListener('mouseover', function () {
                            stars.forEach(s => s.classList.remove('hover'));
                            for (let i = 0; i <= index; i++) {
                                stars[i].classList.add('hover');
                            }
                        });

                        // 滑鼠移出後，恢復到已點選的狀態
                        star.addEventListener('mouseout', function () {
                            stars.forEach(s => s.classList.remove('hover'));
                        });
                    });
                </script>


            </div>
        </div>
    </div>
    <!-- 頁尾 End -->



    <!-- 回到頁首(Top 箭頭 -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <!-- <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script> -->
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
<?php
// 關閉資料庫連接
mysqli_close($link);
?>