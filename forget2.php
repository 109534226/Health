<!--
Author: W3layouts
Author URL: http://w3layouts.com
-->
<!DOCTYPE html>
<html lang="zxx">

<head>
	<title>健康醫療網站-變更密碼</title>

	<!-- Meta tags -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
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

	<!-- Style -->
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />

	<script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const email = urlParams.get('email');
            if (email) {
                const emailInput = document.getElementById('email');
                emailInput.value = decodeURIComponent(email);
                emailInput.readOnly = true; // 設置為只讀
            }
        };
    </script>
</head>

<body>
	<!-- 頁首 Start -->
	<div class="container-fluid sticky-top bg-white shadow-sm">
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
						<a href="u_index.php" class="nav-item nav-link">首頁</a>
						<a href="u_medical.php" class="nav-item nav-link">相關醫療資訊</a>
						<a href="u_map.php" class="nav-item nav-link">預約及現場掛號人數</a>
						<a href="u_story.php" class="nav-item nav-link">患者故事與經驗分享</a>
						<a href="login.php" class="nav-item nav-link active">登入</a>
					</div>
				</div>
			</nav>
		</div>
	</div>
	<!-- 頁首 End -->

	<!-- 登入 start -->
	<section class="w3l-login">
		<div class="overlay">
			<div class="wrapper">
				<div class="logo">
					<a class="brand-logo" href="index.php">健康醫療網站</a>
				</div>
				<div class="form-section">
					<h3>忘記密碼</h3>
					<h6> </h6>
					<form action="忘記密碼2.php" method="post" class="signin-form">
						
						<div class="form-input">
							<input type="text" id="email" name="email" required="" readonly>
						</div>
						<div class="form-input">
							<input type="text" name="newpsd" placeholder="新密碼" required="">
						</div>
						<div class="form-input">
							<input type="text" name="newpsd2" placeholder="確認新密碼" required="">
						</div>
						<button type="submit" class="btn btn-primary theme-button mt-4">確定</button>
					</form>
				</div>
			</div>
		</div>
		<!-- 添加驗證新密碼與確認新密碼 -->
		<script>
			function validatePasswords(event) {
				const newPassword = document.querySelector('input[name="newpsd"]').value;
				const confirmPassword = document.querySelector('input[name="newpsd2"]').value;

				if (newPassword !== confirmPassword) {
					event.preventDefault(); // 阻止表單提交
					alert('新密碼與確認新密碼不一致，請重新輸入。');
				}
			}

			// 將validatePasswords函式綁定到表單的submit事件
			document.querySelector('.signin-form').addEventListener('submit', validatePasswords);
		</script>

		<div id='stars'></div>
		<div id='stars2'></div>
		<div id='stars3'></div>
	</section>
	<!-- 登入 end -->

	<!-- 回到頁首(Top 箭頭)  -->
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