<!DOCTYPE html>
<html lang="en">

<head>
    <title>健康醫療網站</title>
    <meta charset="UTF-8">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 使用 fetch 獲取 PHP 的資料
            fetch('文章.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('網路請求失敗，狀態碼：' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data); // 查看返回的資料

                    const healthContainer = document.getElementById('health-articles-container');
                    const diseaseContainer = document.getElementById('disease-articles-container');

                    // 顯示健康相關文章
                    data.health.forEach(item => {
                        const articleDiv = document.createElement('div');
                        articleDiv.classList.add('col-xl-4', 'col-lg-6');
                        articleDiv.innerHTML = `
                            <div class="bg-light rounded overflow-hidden">
                                <img class="img-fluid w-100" src="${item.image}" alt="">
                                <div class="p-4">
                                    <a class="h3 d-block mb-3" href="${item.url}">${item.title}</a>
                                    <p class="m-0">${item.subtitle}</p>
                                    <p style="color: black;font-size:13px;">資料來源: ${item.source}</p>
                                </div>
                            </div>
                        `;
                        healthContainer.appendChild(articleDiv);
                    });

                    // 顯示疾病相關文章
                    data.disease.forEach(item => {
                        const articleDiv = document.createElement('div');
                        articleDiv.classList.add('col-xl-4', 'col-lg-6');
                        articleDiv.innerHTML = `
                            <div class="bg-light rounded overflow-hidden">
                                <img class="img-fluid w-100" src="${item.image}" alt="">
                                <div class="p-4">
                                    <a class="h3 d-block mb-3" href="${item.url}">${item.title}</a>
                                    <p class="m-0">${item.subtitle}</p>
                                    <p style="color: black;font-size:13px;">資料來源: ${item.source}</p>
                                </div>
                            </div>
                        `;
                        diseaseContainer.appendChild(articleDiv);
                    });
                })
                .catch(error => console.error('錯誤:', error));
        });
    </script>


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
                        <a href="medical.php" class="nav-item nav-link active">相關醫療資訊</a>
                        <a href="map.php" class="nav-item nav-link">預約及現場掛號人數</a>
                        <a href="story.php" class="nav-item nav-link">患者故事與經驗分享</a>
                        <a href="login.php" class="nav-item nav-link">登入</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- 頁首 End -->

    <!-- 相關醫療資訊 Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <!-- 健康相關文章 -->
            <div class="text-center mx-auto mb-5" style="max-width: 500px;">
                <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">醫療訊息</h5>
                <h3></br></h3>
                <h1 class="display-4">健康相關文章</h1>
            </div>
            <div class="row g-5" id="health-articles-container">
                <!-- 健康文章動態生成 -->
            </div>

            <!-- 疾病相關文章 -->
            <div class="text-center mx-auto mt-5 mb-5" style="max-width: 500px;">
                <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">疾病訊息</h5>
                <h3></br></h3>
                <h1 class="display-4">疾病相關文章</h1>
            </div>
            <div class="row g-5" id="disease-articles-container">
                <!-- 疾病文章動態生成 -->
            </div>
        </div>
    </div>




    <!-- 頁尾 Start -->
    <div class="container-fluid bg-dark text-light mt-5 py-5">
        <div class="container py-5">
            <div class="row g-5">
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