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

    <!-- 頁首 Start -->
    <div class="container-fluid sticky-top bg-white shadow-sm mb-5">
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-3 py-lg-0">
                <a href="index.html" class="navbar-brand">
                    <h1 class="m-0 text-uppercase text-primary"><i class="fa fa-clinic-medical me-2"></i>健康醫療網站</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="u_index.php" class="nav-item nav-link">首頁</a>
                        <a href="u_medical.php" class="nav-item nav-link">相關醫療資訊</a>
                        <a href="u_map.php" class="nav-item nav-link">預約及現場掛號人數</a>
                        <a href="u_story.php" class="nav-item nav-link  active">患者故事與經驗分享</a>
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

    <!-- 故事 start-->
    <div class="container-fluid py-5">
        <!-- 標題 start-->
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 500px;">
                <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">悄悄話</h5>
                <h3></br></h3>
                <h1 class="display-5">患者故事與經驗分享</h1>
            </div>
        </div>

        <!-- 關鍵字搜尋 start
        <div class="mb-5">
            <div class="input-group">
                <input type="text" class="form-control p-3" placeholder="Keyword">
                <button class="btn btn-primary px-3"><i class="fa fa-search"></i></button>
            </div>
        </div> -->

        <!-- 故事分享 Start -->
        <div class="mb-5">
            <img class="img-fluid w-100 rounded mb-5" src="img/story.jpg" alt="">
            <h1 class="mb-4"><a href="https://www.commonhealth.com.tw/blog/1933">讓病人多表達自己真正的想法──分享一個看病經驗</h1></a>
            <h5>
                <p>我分享一個看病的經驗。
                    </br>
                    </br>
                    幾年前，有位資深、頗負盛名的表演家正在準備一個演出，他從事表演工作已經幾十年，演出似乎沒有甚麼特別，但近幾個月來，他一直思考自己是否要親自上台表演。這次，也許是他最後一次正式在台前演出，為此他已經有幾個禮拜失眠，甚至有幾次在排演時發生暈眩的狀況。他求助一位主任級醫師，做完檢查後，我這位老師正好也準備退休，因此給他的建議是：人要服老，趕快交棒給年輕人，不要再煩惱演出的事，在台下作個快樂的觀眾，失眠、暈眩自可以不藥而癒；最後丟下一句話，「我要退休了，如果以後還有問題請找胡醫師」。
                    </br>
                    </br>
                    過了不久，我接到院長的電話，通知我有位病人要來我的門診，還特別交代，一定要解決他的失眠問題。我們這位院長年輕有為，剛接任院長，正準備大展鴻圖，他聽了病人的問題，馬上說，一定要演出，這是小事一件，失眠只是因為焦慮罷了，這是演出前的正常現象，也許用點藥就可解決，千萬不要因此打退堂鼓，最後也丟下一句話，「我會安排胡醫師來幫忙」。
                    </br>
                    </br>
                    病人跟我詳細講了他的困擾及兩位醫師的意見，他覺得兩位醫師都給他很好的建議，卻讓他無所適從。在舞台上演出已經幾十年，他非常熱愛這個工作，舞台演出可以說是其生命中最重要的一部分，但隨著年齡增加，他慢慢也覺得自己的表現走下坡，似乎到了該為自己的演出畫一個完美句點的時候，同時他的學生們也可以取代他，好好在舞台上發光發亮，完成他多年的心願。總之，他理解該快樂交棒；可是他又擔心，沒有他的演出，觀眾會不會不習慣，這個表演團隊可能無法生存，會不會就此走入歷史？（我感覺到他對舞台還是戀眷的。）
                    </br>
                    </br>
                    門診前，我一點也沒想到失眠背後有這麼沉重的問題；我們找來他的行政助理，重新安排他的行程，仍保留一點他的演出，行政工作則全部丟出去給學生；另外，我還是處方了安眠藥給他，並給他我的手機號碼，主要讓他安心，萬一他失眠，是有所依靠。
                    </br>
                    </br>
                    好幾年過去，他偶而會來門診，特別是在重要演出前，我們會談一些他最近的演出，大多是非常成功愉快，表演團隊運作良好，我處方一點安眠藥給他，他不一定使用，但他會安心。我不知道他何時會真正從舞台退休。
                    </br> </br>
                    我與學生花比較長的時間一起看每位病人，通常學生們會先問病史（疾病的發生及進展），做身體檢查，簡單的跟病人說明病情，我再做一些補充，等病人離開後，我與學生會有一些討論。幾年下來學生、病人似乎都很滿意，學生覺得教學門診讓他們有難得練習的機會，也得到即時的回饋。其實，詢問病史對學生來說並不是一件容易的事，要在有限的時間裡問到該問的，又不遺漏重要訊息是一個挑戰，更何況接著他們要在我面前做完一套神經學檢查，因此他們都準備了各式各樣的「口訣」，例如LQQOPERA：症狀的位置（Location）、質與量（Quality
                    & Quantity）、發生時間（Onset）、前驅加重緩解因子（Precipitating,Exaggerating,Relieving factors），最後問到伴隨症狀（Accompanying
                    symptoms/signs）。
                    </br> </br>
                    有時候，我會發現除了這幾個問題以外的訊息，學生們都不在意，他們只收集他們要的資訊，才好在病人及老師面前很快地做診斷。
                    </br> </br>
                    教學最好的地方是可以同時學習，我反省自己，在一般門診時間壓力下，病人剛講了哪裡不舒服，我就開始一連串的問題，先把最嚴重的疾病排除，然後病人就跟隨我的節奏把門診快速完成。我與我的學生其實都應該多傾聽，讓病人多表達自己的想法，我常羨慕那些病人願意跟他「掏心掏肺」、無所不談的醫生，因此現在的我會問完相干問題後，會再多問他們一些也許跟這次疾病「不相干」的問題，我想也許這些不相干的問題，藏著許多病人自己真正的想法。
                    </br> </br>
                </p>

                <p>
                    本文載於<a href="https://www.peoplenews.tw/">《民報_醫病平台》</a>2016-11-15．出處 ／ Web only．圖片來源 ／ Unsplash
                </p>
            </h5>
        </div>

        <!-- 留言區 Start -->
        <?php
        include 'db.php'; // 引入資料庫連線文件
        
        // 查詢公開分享的留言資料，按時間排序
        $query = "SELECT id, username, comment, time, likes FROM story WHERE share = '公開' ORDER BY time ASC";
        $result = mysqli_query($link, $query);

        // 檢查是否有留言資料
        if (mysqli_num_rows($result) > 0) {
            echo '<h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 mb-4">看看其他人的留言吧！</h4>';

            // 直接顯示所有留言
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['id'];
                $username = htmlspecialchars($row['username']); // 避免XSS攻擊的用戶名稱轉義
                $comment = htmlspecialchars($row['comment']); // 避免XSS攻擊的留言內容轉義
                $time = date("Y-m-d H:i", strtotime($row['time'])); // 格式化時間
                $likes = $row['likes'];

                // 顯示留言
                echo "
        <div class='d-flex mb-4 comment' data-id='$id'>
            <img src='img/300.jpg' class='img-fluid rounded-circle' style='width: 45px; height: 45px;'>
            <div class='ps-3'>
                <h6><a href='#'>$username</a> <small><i>$time</i></small></h6>
                <p>$comment</p>
                <button class='btn btn-light btn-sm likeButton' onclick='toggleLike(this, $id)'>👍<span class='likeCount'>$likes</span></button>
            </div>
        </div>
        ";
            }
        } else {
            echo "<p>目前沒有公開的留言。</p>"; // 若無留言則顯示此訊息
        }

        mysqli_close($link); // 關閉資料庫連線
        ?>

        <script>
            // 按讚功能
            window.toggleLike = function (button, id) {
                const likeCountElement = button.querySelector('.likeCount'); // 取得按讚數顯示元素
                let currentLikeCount = parseInt(likeCountElement.textContent); // 目前按讚數
                let isLiked = button.classList.toggle('liked'); // 切換按讚狀態
                let action = isLiked ? 'like' : 'unlike'; // 判斷執行的動作

                // 根據按讚狀態更新按讚數
                if (action === 'like') {
                    likeCountElement.textContent = currentLikeCount + 1;
                } else {
                    likeCountElement.textContent = currentLikeCount - 1;
                }

                // 發送按讚/取消按讚請求到後端
                fetch('like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${id}&action=${action}` // 傳遞留言ID與動作類型
                })
                    .then(response => response.text())
                    .then(data => {
                        // 判斷伺服器回傳結果，顯示錯誤提示
                        if (data.startsWith('error')) {
                            alert("按讚請求失敗: " + data);
                            button.classList.toggle('liked'); // 還原按讚狀態
                            likeCountElement.textContent = currentLikeCount; // 還原按讚數
                        } else {
                            likeCountElement.textContent = data; // 更新按讚數為伺服器返回的數值
                        }
                    })
                    .catch(error => {
                        // 捕捉並提示錯誤
                        alert('按讚錯誤: ' + error);
                        button.classList.toggle('liked'); // 還原按讚狀態
                        likeCountElement.textContent = currentLikeCount; // 還原按讚數
                    });
            }
        </script>
        <!-- 留言區 End -->

        <!-- 分享故事 Start -->
        <div class="bg-light rounded p-5">
            <h4 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-white mb-4">分享你的故事</h4>
            <form action="故事.php" method="POST">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <input type="text" name="nickname" class="form-control bg-white border-0"
                            value="<?php echo htmlspecialchars($姓名); ?>" style="height: 55px;" required>
                    </div>
                    <div class="col-12">
                        <textarea name="comment" class="form-control bg-white border-0" rows="5" placeholder="評論(至少十個字)"
                            required></textarea>
                    </div>
                    <div class="col-12">
                        <label for="share_option">是否公開分享文章:</label>
                        <select id="share_option" name="share_option" class="form-control bg-white border-0" required>
                            <option value="" disabled selected>選擇是否公開</option>
                            <option value="公開">公開</option>
                            <option value="不公開">不公開</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary w-100 py-3" type="submit">發布</button>
                    </div>
                </div>
            </form>
        </div>

        <script>
            // 表單防呆檢查
            document.getElementById('storyForm').addEventListener('submit', function (event) {
                const nicknameField = document.getElementsByName('nickname')[0];
                const commentField = document.getElementsByName('comment')[0];
                const shareOptionField = document.getElementById('share_option');

                // 檢查暱稱格式
                if (!document.getElementById('anonymous').checked) {
                    const nickname = nicknameField.value.trim();
                    const nicknameRegex = /^[\u4e00-\u9fa5_a-zA-Z0-9]+$/;
                    if (nickname === "") {
                        alert('請輸入暱稱');
                        event.preventDefault();
                        return;
                    } else if (!nicknameRegex.test(nickname)) {
                        alert('暱稱僅能包含中文、英文字母或數字，且不能包含特殊字元');
                        event.preventDefault();
                        return;
                    }
                }

                // 檢查評論字數是否超過 10 個字元
                const comment = commentField.value.trim();
                if (comment.length < 10) {
                    alert('評論至少需要 10 個字元');
                    event.preventDefault();
                    return;
                }

                // 檢查是否選擇了公開或不公開選項
                if (shareOptionField.value === "") {
                    alert('請選擇是否公開分享文章');
                    event.preventDefault();
                    return;
                }
            });
        </script>
        <!-- 分享故事 End -->


        <!-- 頁尾 Start -->
        <div class="container-fluid bg-dark text-light mt-5 py-5">
            <div class="container py-5">
                <div class="row g-5">
                    <!-- 快速連結 -->
                    <div class="col-lg-3 col-md-6">
                        <h4
                            class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
                            快速連結</h4>
                        <div class="d-flex flex-column justify-content-start">
                            <a class="text-light mb-2" href="u_index.php"><i class="fa fa-angle-right me-2"></i>首頁</a>
                            <a class="text-light mb-2" href="u_medical.php"><i
                                    class="fa fa-angle-right me-2"></i>相關醫療資訊</a>
                            <a class="text-light mb-2" href="u_map.php"><i
                                    class="fa fa-angle-right me-2"></i>預約及現場掛號人數</a>
                            <a class="text-light mb-2" href="u_story.php"><i
                                    class="fa fa-angle-right me-2"></i>患者故事與經驗分享</a>
                            <a class="text-light mb-2" href="u_profile.php"><i
                                    class="fa fa-angle-right me-2"></i>關於我</a>
                        </div>
                    </div>
                    <!-- 評分 -->
                    <div class="col-lg-3 col-md-6">
                        <h4
                            class="d-inline-block text-primary text-uppercase border-bottom border-5 border-secondary mb-4">
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