<?php
session_start();

include "db.php";

$電子郵件 = $_POST["email"];

if ($電子郵件 == "") {
    echo "<script>
    alert('電子郵件未輸入');
    window.location.href = 'forget.php';
</script>";
}

$電子郵件 = mysqli_real_escape_string($link, $電子郵件);

$SQL指令 = "select * from `user` where `email`='$電子郵件';";

$ret = mysqli_query($link, $SQL指令) or die(mysqli_error($link));

header("Location: forget2.php");

header("Location: forget2.php?email=" . urlencode($電子郵件));

exit; // 確保不繼續執行

?>