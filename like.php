<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action === 'like') {
        $query = "UPDATE story SET likes = likes + 1 WHERE id = $id";
    } else if ($action === 'unlike') {
        $query = "UPDATE story SET likes = likes - 1 WHERE id = $id";
    }

    if (mysqli_query($link, $query)) {
        $result = mysqli_query($link, "SELECT likes FROM story WHERE id = $id");
        $row = mysqli_fetch_assoc($result);
        echo $row['likes'];
    } else {
        echo "error:更新失敗";
    }
} else {
    echo "error:無效的請求";
}

mysqli_close($link);
?>