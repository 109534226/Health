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
    $email = mysqli_real_escape_string($link, $email);
    $grade = mysqli_real_escape_string($link, $grade);
    $name = mysqli_real_escape_string($link, $name);
    $password = mysqli_real_escape_string($link, $password);


    // SQL 插入指令
    $sql = "INSERT INTO user (username,name,password, email, grade) VALUES ('$username','$name','$password', '$email', '$grade')";
    


    // 使用 mysqli_query 執行 SQL
    if (mysqli_query($link, $sql)) {
        echo "";
        echo "<script>
                alert('新用戶新增成功');
                window.location.href = '新增用戶.php';
              </script>";

    } else {
        echo "新增失敗: " . mysqli_error($link);
    }

    // 關閉連接
    mysqli_close($link);
}
?>
