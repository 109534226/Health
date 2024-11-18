<?php
// 连接数据库
include 'db.php';


// 获取评分值
$score = $_POST['score'];

// 插入评分数据到数据库
$sql = "INSERT INTO score ( score) VALUES ('$score')";
if (mysqli_query($link, $sql)) {
    echo "評分成功！";
} else {
    echo "錯誤：" . mysqli_error($link);
}
?>
