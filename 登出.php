<?php
session_start();
// 清除所有 session
session_unset();
session_destroy();
// 重定向到登錄頁面，附加提示訊息
header("Location: login.php?message=logged_out");
exit();
?>
