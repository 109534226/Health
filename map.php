<?php

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
    $county = trim($_POST['county'] ?? '');
    $district = trim($_POST['district'] ?? '');

    // 驗證請求參數
    if (empty($county) || empty($district)) {
        echo json_encode(["message" => "請選擇縣市和地區！"]);
        exit;
    }

    // 引入資料庫連線
    include 'db.php';

    // 查詢資料庫
    $query = "SELECT DISTINCT `醫事機構` FROM `hospital` WHERE `縣市名稱` = ? AND `區域` = ?";
    $stmt = $link->prepare($query);

    if (!$stmt) {
        echo json_encode(["message" => "伺服器內部錯誤，請稍後再試。"]);
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
            $clinics[] = htmlspecialchars($row['醫事機構'], ENT_QUOTES, 'UTF-8');
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
    <meta charset="utf-8">
    <title>健康醫療網站</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        <a href="index.php" class="nav-item nav-link">首頁</a>
                        <a href="medical.php" class="nav-item nav-link">相關醫療資訊</a>
                        <a href="map.php" class="nav-item nav-link active">預約及現場掛號人數</a>
                        <a href="story.php" class="nav-item nav-link">患者故事與經驗分享</a>
                        <a href="login.php" class="nav-item nav-link">登入</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- 頁首 End -->

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
                    <a class="btn btn-dark rounded-pill py-3 px-5 me-3" href="#" onclick="showAlert()">預約</a>
                    <script>
                        function showAlert() {
                            swal("健康醫療網站提醒您", "請先登入帳號！！", "warning");
                        }
                    </script>
                </div>
                <div class="col-lg-6">
                    <div class="bg-white text-center rounded p-5">
                        <h1 class="mb-4">交通工具</h1>
                        <form>
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

                                <div class="col-12 col-sm-6" style="width: 100%; max-width: 600px;">
                                    <!-- 診所下拉選單 -->
                                    <select class="form-select bg-light border-0" style="height: 55px;" id="clinic"
                                        name="clinic">
                                        <option selected value="" disabled>選擇診所或醫院</option>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($row['醫事機構'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($row['醫事機構'], ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                        } else {
                                            echo "<option value='' disabled>無可用醫事機構</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <script>
                                    // 診所選擇事件：修改 placeholder 並同步值
                                    $('#clinic').on('change', function () {
                                        const selectedClinic = $(this).val();
                                        const locationInput = $('#location');

                                        if (selectedClinic) {
                                            locationInput.val(selectedClinic);
                                            locationInput.attr('placeholder', `目前選擇：${selectedClinic}`);
                                        } else {
                                            locationInput.val('');
                                            locationInput.attr('placeholder', '搜尋醫院或診所');
                                        }
                                    });



                                    // 請求診所列表
                                    $('#district_box').on('change', function () {
                                        const county = $('#county_box').val();
                                        const district = $(this).val();

                                        if (!county || !district) {
                                            alert("請先選擇縣市和地區！");
                                            return;
                                        }

                                        $.ajax({
                                            url: 'map.php',
                                            type: 'POST',
                                            data: {
                                                action: 'get_clinics',
                                                county: county,
                                                district: district
                                            },
                                            success: function (response) {
                                                const data = JSON.parse(response);
                                                $('#clinic').empty().append('<option value="" disabled selected>選擇診所或醫院</option>');

                                                if (data.clinics) {
                                                    data.clinics.forEach(function (clinic) {
                                                        $('#clinic').append(`<option value="${clinic}">${clinic}</option>`);
                                                    });
                                                } else if (data.message) {
                                                    alert(data.message);
                                                }
                                            },
                                            error: function () {
                                                alert("發生錯誤，請稍後再試！");
                                            }
                                        });
                                    });

                                    // $(document).ready(function () {
                                    //     // 當用戶更改縣市或地區時，觸發事件
                                    //     $('#county_box, #district_box').on('change', function () {
                                    //         // 獲取縣市和地區的選擇值
                                    //         const county = $('#county_box').val();
                                    //         const district = $('#district_box').val();

                                    //         // 如果縣市和地區都有選擇，則顯示結果
                                    //         if (county && district) {
                                    //             alert(`您選擇的縣市是：${county}\n您選擇的地區是：${district}`);
                                    //         }
                                    //     });
                                    // });
                                </script>

                                <div class="row">
                                    <p><br /></p>
                                    <div class="col-md-6 mb-3">
                                        <button class="btn btn-primary  py-3" style="width: 100%"
                                            type="submit">查看現場掛號人數</button>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <button class="btn btn-primary  py-3" style="width: 100%" type="submit"
                                            onclick="sLocation()">搜尋路線/交通方式</button>
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
                        <a class="text-light mb-2" href="index.php"><i class="fa fa-angle-right me-2"></i>首頁</a>
                        <a class="text-light mb-2" href="medical.php"><i class="fa fa-angle-right me-2"></i>相關醫療資訊</a>
                        <a class="text-light mb-2" href="map.php"><i class="fa fa-angle-right me-2"></i>預約及現場掛號人數</a>
                        <a class="text-light mb-2" href="story.php"><i class="fa fa-angle-right me-2"></i>患者故事與經驗分享</a>
                        <a class="text-light mb-2" href="login.php"><i class="fa fa-angle-right me-2"></i>登入</a>
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


                <!-- 通訊 -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
                        通訊</h4>
                    <form action="">
                        <div class="input-group">
                            <input type="text" class="form-control p-3 border-0" placeholder="輸入您的電子郵件">
                            <input class="btn btn-primary" type="button" value="註冊"
                                onclick="location.href='register.php'">
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!-- 頁尾 End -->


    <!-- 回到頁首(Top 箭頭-->
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