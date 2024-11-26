<?php
session_start();

if (!isset($_SESSION["登入狀態"])) {
    header("Location: login.html");
    exit;
}

// 防止頁面被瀏覽器緩存
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

// 檢查 "帳號" 和 "姓名" 是否存在於 $_SESSION 中
if (isset($_SESSION["帳號"]) && isset($_SESSION["姓名"])) {
    // 獲取用戶帳號和姓名
    $帳號 = $_SESSION['帳號'];
    $姓名 = $_SESSION['姓名'];
} else {
    echo "<script>
            alert('會話過期或資料遺失，請重新登入。');
            window.location.href = 'login.html';
          </script>";
    exit();
}
// 防止頁面被瀏覽器緩存
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");
// header('Content-Type: application/json; charset=utf-8');

// 清空輸出緩存
ob_clean();
flush();

// 檢查是否為 POST 請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 驗證操作類型
    if ($action !== 'get_clinics') {
        echo json_encode(["message" => "無效的操作類型。"]);
        exit;
    }

    // 獲取請求參數
    $county = trim($_POST['county'] ?? ''); // 縣市名稱
    $district = trim($_POST['district'] ?? ''); // 地區名稱

    // 驗證請求參數
    if (empty($county) || empty($district)) {
        echo json_encode(["message" => "請選擇縣市和地區！"]);
        exit;
    }

    // 引入資料庫連線
    include 'db.php';

    // 查詢資料庫
    $query = "SELECT DISTINCT `hospital` FROM `hospital` WHERE `city` = ? AND `area` = ?";
    $stmt = $link->prepare($query);

    if (!$stmt) {
        echo json_encode(["message" => "使服器內部錯誤，請稍後再試。"]);
        exit;
    }

    // 綁定參數並執行查詢
    $stmt->bind_param('ss', $county, $district);
    $stmt->execute();
    $result = $stmt->get_result();

    // 處理結果
    if ($result->num_rows > 0) {
        $clinics = [];
        while ($row = $result->fetch_assoc()) {
            $clinics[] = htmlspecialchars($row['hospital'], ENT_QUOTES, 'UTF-8');
        }
        echo json_encode(["clinics" => $clinics]);
    } else {
        echo json_encode(["message" => "未找到符合條件的診所或醫院。"]);
    }

    // 關閉連線
    $stmt->close();
    $link->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>健康醫療網站</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <!-- SweetAlert JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

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
    <!-- 頁首 stsrt-->
    <div class="container-fluid sticky-top bg-white shadow-sm">
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
                        <a href="u_index.php" class="nav-item nav-link active">首頁</a>
                        <a href="u_medical.php" class="nav-item nav-link">相關醫療資訊</a>
                        <a href="u_map.php" class="nav-item nav-link">預約及現場掛號人數</a>
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
    <!-- 頁首 strat-->

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


    <!-- 首頁 start-->
    <div class="container-fluid bg-primary py-5 mb-5 hero-header">
        <div class="container py-5">
            <div class="row justify-content-start">
                <div class="col-lg-8 text-center text-lg-start">
                    <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5"
                        style="border-color: rgba(256, 256, 256, .3) !important;">歡迎使用健康醫療網站</h5>
                    <h1 class="display-5" ;style="float:right">
                        健康醫療網站致力於成為您可信賴的健康資訊與醫療服務平台。我們提供全面的醫療知識、專業醫師諮詢，以及便捷的線上預約服務，讓您隨時隨地掌握最新的健康動態。</h1>
                </div>
            </div>
        </div>
    </div>
    <!-- 首頁 End -->

    <!-- 首頁介紹 Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row gx-5">
                <div class="col-lg-5 mb-5 mb-lg-0" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute w-100 h-100 rounded" src="img/doctor.jpg"
                            style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="mb-4">
                        <h1 class="display-4 d-inline-block text-uppercase border-bottom border-5">我們的服務</h1>
                    </div>
                    <p style="font-size:23px;"><strong>
                            相關醫療資訊</br>
                        </strong></p>
                    <p style="font-size:20px;">
                        我們的知識庫包含了數千篇經專業醫師審核的醫療文章，涵蓋從常見病症到專科治療的各類資訊。</br></br>
                    </p>
                    <p style="font-size:23px;"><strong>
                            預約及現場掛號人數</br>
                        </strong></p>
                    <p style="font-size:20px;">
                        為您提供便捷的線上掛號與預約服務。您可以輕鬆選擇合適的醫師及就診時間，避免繁瑣的排隊程序。</br></br>
                    </p>
                    <p style="font-size:23px;"><strong>
                            交通工具</br>
                        </strong></p>
                    <p style="font-size:20px;">
                        我們的專業交通工具，無論您是想了解某種交通工具的特性，還是尋求有效的出行方案，我們的資源都將為您提供清晰且具參考價值的資訊，幫助您做出明智的交通工具選擇。
                    </p>

                </div>
            </div>
        </div>
    </div>
    <!-- 首頁介紹 end -->

    <!-- 相關醫療資訊  Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 500px;">
                <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">醫療訊息</h5>
                <h3></br></h3>
                <h1 class="display-4">健康相關文章</h1>
            </div>

            <div class="row g-5">
                <div class="col-xl-4 col-lg-6">
                    <div class="bg-light rounded overflow-hidden">
                        <img class="img-fluid w-100" src="img/blog-1.jpg" alt="">
                        <div class="p-4">
                            <a class="h3 d-block mb-3"
                                href="https://www.cw.com.tw/article/5130051?from=search">運動改變大腦　憂鬱族群動起來健康益處翻倍</a>
                            <p class="m-0">運動可以透過改變大腦，進而增進身心健康。心情Blue或是受憂鬱症所苦的人們，可從運動獲得的好處更是翻倍。做什麼運動有差嗎？</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6">
                    <div class="bg-light rounded overflow-hidden">
                        <img class="img-fluid w-100" src="img/blog-2.jpg" alt="">
                        <div class="p-4">
                            <a class="h3 d-block mb-3"
                                href="https://www.cw.com.tw/article/5129945?from=search">冰淇淋真的對健康很不好嗎？答案可能讓你意外</a>
                            <p class="m-0">如果你每天都吃冰淇淋，會發生很嚴重的事嗎？你的健康會因此崩潰嗎？聽聽專業營養學家怎麼說。</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6">
                    <div class="bg-light rounded overflow-hidden">
                        <img class="img-fluid w-100" src="img/blog-3.jpg" alt="">
                        <div class="p-4">
                            <a class="h3 d-block mb-3" href="">全球18億人因懶得動，而面臨健康風險</a>
                            <p class="m-0">人們似乎越來越「懶得動」。如果這種趨勢持續下去，2030年，全球缺乏運動的人口比例將達到35%。你是其中一員嗎？</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mx-auto mb-5" style="max-width: 1000px;">
                    <h3></br></h3>
                    <h1 class="display-4">疾病相關文章</h1>
                </div>

                <div class="col-xl-4 col-lg-6">
                    <div class="bg-light rounded overflow-hidden">
                        <img class="img-fluid w-100" src="img/blog-10.jpg" alt="">
                        <div class="p-4">
                            <a class="h3 d-block mb-3" href="https://www.cw.com.tw/aging/article/5129689">國衛院專欄｜
                                預防心臟病，降低腦疾病！時時檢視8大健康指標活得更安心</a>
                            <p class="m-0">心臟病、失智症、腦中風等老年人常見的疾病可能有相同危險因子，甚至因果關係。8個健康指標幫助你促進心血管健康，對腦健康更有益。</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6">
                    <div class="bg-light rounded overflow-hidden">
                        <img class="img-fluid w-100" src="img/blog-11.jpg" alt="">
                        <div class="p-4">
                            <a class="h3 d-block mb-3"
                                href="https://www.cw.com.tw/aging/article/5130979">「在宅急症照護」新制上路，失能長輩染3疾病可在家住院、不用等病床！</a>
                            <p class="m-0">俗稱「在宅住院」的「全民健康保險在宅急症照護試辦計畫」7月上路，未來在家就可以得到醫療照護，而不用到醫院嗎？誰可以用到這項服務？</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-6">
                    <div class="bg-light rounded overflow-hidden">
                        <img class="img-fluid w-100" src="img/blog-12.jpg" alt="">
                        <div class="p-4">
                            <a class="h3 d-block mb-3"
                                href="https://www.cw.com.tw/article/5128439?from=search">下一波全球疾病大流行？</a>
                            <p class="m-0">【2024全球大趨勢｜科技】一場針對小麥、水稻、馬鈴薯等重要糧食作物的疫情，正在席捲全球，我們必須採取行動以杜絕禍害，否則將爆發大規模飢荒。</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- 相關醫療資訊  End -->

    <!-- 交通工具 Start -->
    <div class="container-fluid bg-primary my-5 py-5">
        <div class="container py-5">
            <div class="row gx-5">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="mb-4">
                        <h5 class="d-inline-block text-white text-uppercase border-bottom border-5">搜尋</h5>
                        <p></br></p>
                        <h1 class="display-4">請選擇您要去的醫療診所</h1>
                    </div>
                    <p class="text-white mb-5">
                        歡迎使用交通工具查詢系統！我們致力於為您提供最全面、最便捷的交通工具資訊，在這裡找到最適合的解決方案我們將為您推薦最合適的交通工具及路線，讓您輕鬆實現想去哪間醫院的出行願望。感謝您選擇我們的交通工具查詢系統，我們將竭誠為您提供最優質的服務，幫助您暢行無阻，享受每一段旅程的美好！
                    </p>
                    <a class="btn btn-dark rounded-pill py-3 px-5 me-3" href="u_reserve.php">預約</a>
                </div>
                <div class="col-lg-6">
                    <div class="bg-white text-center rounded p-5">
                        <h1 class="mb-4">交通工具</h1>
                        <form action="u_selectclinic2.php" method="POST">
                            <div class="row g-3">
                                <div class="mx-auto" style="width: 100%; max-width: 600px;">
                                    <div class="input-group">
                                        <!-- 搜尋輸入框 -->
                                        <input type="text" class="form-control border-primary w-50" id="location"
                                            placeholder="搜尋醫院或診所">
                                        <button class="btn btn-dark border-0 w-25"
                                            onclick="searchLocation()">查詢</button>
                                    </div>
                                </div>

                                <script>
                                    // 查詢功能
                                    function searchLocation() {
                                        const location = document.getElementById("location").value;
                                        if (location) {
                                            const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location)}`;
                                            window.open(url, "_blank");
                                        } else {
                                            alert("請輸入地點");
                                        }
                                    }                                    
                                </script>

                                <div class="col-12 col-sm-6">
                                    <select class="form-select bg-light border-0" style="height: 55px;" name="county"
                                        id="county_box">
                                        <option selected value="">選擇縣市</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <select class="form-select bg-light border-0" style="height: 55px;" name="district"
                                        id="district_box">
                                        <option selected value="">選擇鄉鎮市區</option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <!-- 診所下拉選單 -->
                                    <select class="form-select bg-light border-0" style="height: 55px;" id="clinic"
                                        name="clinic">
                                        <option selected value="">選擇診所或醫院</option>
                                        <?php
                                        // 使用 mysqli 查詢診所列表
                                        $sql = "SELECT DISTINCT 醫事機構 FROM hospital";
                                        $result = mysqli_query($conn, $sql);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<option value='" . htmlspecialchars($row['醫事機構'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($row['醫事機構'], ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <!-- 科別下拉選單 -->
                                    <select class="form-select bg-light border-0" style="height: 55px;" id="department"
                                        name="department">
                                        <option selected value="">選擇看診科目</option>
                                    </select>
                                </div>

                                <script>
                                    $(document).ready(function () {
                                        // 當地區選項改變時，請求對應診所列表
                                        $('#district_box').on('change', function () {
                                            const county = $('#county_box').val();
                                            const district = $(this).val();

                                            if (!county || !district) {
                                                alert("請先選擇縣市和地區！");
                                                return;
                                            }

                                            $.ajax({
                                                url: 'u_reserve.php', // 請替換為你的 PHP 文件路徑
                                                type: 'POST',
                                                data: {
                                                    action: 'get_clinics',
                                                    county: county,
                                                    district: district
                                                },
                                                success: function (response) {
                                                    try {
                                                        const data = JSON.parse(response);
                                                        $('#clinic').empty().append('<option value="" disabled selected>選擇診所或醫院</option>');

                                                        if (data.clinics) {
                                                            data.clinics.forEach(function (clinic) {
                                                                $('#clinic').append(`<option value="${clinic}">${clinic}</option>`);
                                                            });
                                                        } else if (data.message) {
                                                            alert(data.message);
                                                        }
                                                    } catch (e) {
                                                        console.error("JSON 解析失敗:", e);
                                                        alert("發生錯誤，無法載入診所列表！");
                                                    }
                                                },
                                                error: function () {
                                                    alert("發生錯誤，請稍後再試！");
                                                }
                                            });
                                        });

                                        // 當選擇診所後請求科別
                                        $('#clinic').on('change', function () {
                                            const selectedClinic = $(this).val();
                                            const departmentSelect = $('#department');

                                            departmentSelect.empty().append('<option value="" selected disabled>選擇看診科目</option>');

                                            if (selectedClinic) {
                                                $.ajax({
                                                    url: '選擇看診科目.php', // 請替換為你的 PHP 文件路徑
                                                    type: 'POST',
                                                    data: {
                                                        clinic: selectedClinic
                                                    },
                                                    success: function (response) {
                                                        departmentSelect.append(response);
                                                    },
                                                    error: function () {
                                                        alert("發生錯誤，無法載入科別！");
                                                    }
                                                });
                                            }
                                        });
                                    });

                                </script>

                                <div class="row">
                                    <p><br /></p>
                                    <div class="col-md-6 mb-3">
                                        <style>
                                            .custom-button {
                                                width: 220%;
                                                height: 50px;
                                            }
                                        </style>
                                        <button class="btn btn-primary custom-button" type="submit">查看現場掛號人數</button>
                                    </div>

                                </div>

                                <script>
                                    function sLocation() {
                                        var location = document.getElementById("location").value;
                                        if (location) {
                                            var url = "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(location);
                                            window.open(url, '_blank');
                                        } else {
                                            alert("請輸入地點");
                                        }
                                    }
                                </script>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 交通工具 End -->

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