<?php
// 引入資料庫連接
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 獲取表單數據
    $id = $_POST['id'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $grade = $_POST['grade'];
    $state = $_POST['state'];

    // 防止 SQL 注入的措施
    $username = mysqli_real_escape_string($link, $username);
    $name = mysqli_real_escape_string($link, $name);
    $password = mysqli_real_escape_string($link, $password);
    $email = mysqli_real_escape_string($link, $email);
    $grade = mysqli_real_escape_string($link, $grade);
    $state = mysqli_real_escape_string($link, $state);

    // SQL 更新指令，去除資料表名稱前的空格
    $sql = "UPDATE `user` SET `username` = '$username', 
    `name` = '$name', 
    'passwird'='$password',
    `email` = '$email', 
    `grade` = '$grade', 
    `state` = '$state'
WHERE `id` = $id";

    // 使用 mysqli_query 執行 SQL
    if (mysqli_query($link, $sql)) {
        echo "<script>
                alert('用戶編輯成功');
                window.location.href = '編輯用戶.php';
              </script>";
    } else {
        echo "編輯失敗: " . mysqli_error($link);
    }

    // 關閉連接
    mysqli_close($link);
}
?>