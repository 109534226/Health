

<!DOCTYPE html>
<html lang="zxx">

<head>
	<title>健康醫療註冊網站</title>

	<!-- Meta tags -->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
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
	<!-- Style -->
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
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
						<a href="index.php" class="nav-item nav-link">首頁</a>
						<a href="medical.php" class="nav-item nav-link">相關醫療資訊</a>
						<a href="map.php" class="nav-item nav-link">預約及現場掛號人數</a>
						<a href="story.php" class="nav-item nav-link">患者故事與經驗分享</a>
						<a href="register.php" class="nav-item nav-link active">註冊</a>
					</div>
				</div>
			</nav>
		</div>
	</div>
	<!-- 頁首 End -->

	<!-- 註冊start-->
	<section class="w3l-login">
		<div class="overlay">
			<div class="wrapper">
				<div class="logo">
					<a class="brand-logo" href="index.php">健康醫療網站</a>
				</div>
				<div class="form-section">
					<h3>註冊新帳號</h3>
					<h6> </h6>
					<form action="註冊.php" method="post" class="signin-form">
						<div class="form-input">
							<input type="text" name="account" placeholder="帳號" required="" autofocus>
						</div>
						<div class="form-input">
							<input type="password" name="password" placeholder="密碼" required="">
						</div>
						<div class="form-input">
							<input type="text" name="name" placeholder="姓名" required="">
						</div>
						<div class="form-input">
							<input type="email" name="email" placeholder="電子郵件" required="">
						</div>
						<button type="submit" class="btn btn-primary theme-button mt-4">註冊</button>
						<!-- <div class="new-signup">
							<a href="#reload" class="signuplink">聯繫客服</a>
						</div> -->
					</form>
					<p class="signup">已經有帳號了? <a href="Login.php" class="signuplink">登入</a></p>
				</div>
			</div>
		</div>
		<div id='stars'></div>
		<div id='stars2'></div>
		<div id='stars3'></div>
	</section>
	<!-- 註冊end -->

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