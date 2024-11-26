<?php
session_start(); // 開啟 session
include "db.php"; // 包含資料庫連接文件

// 確認使用者是否已登入
if (!isset($_SESSION["登入狀態"]) || $_SESSION["登入狀態"] !== true) {
    // 如果 Session 中沒有設定登入狀態，或狀態不為 true，則跳轉到登入頁面
    header("Location: login.php"); // 跳轉到登入頁面
    exit(); // 停止後續程式執行
}

// 從 Session 中獲取使用者的帳號
$帳號 = $_SESSION["帳號"];

// 從 user 表中查詢 user_id
$sql_user = "SELECT user_id FROM user WHERE account = ?";
$stmt_user = $link->prepare($sql_user);
if ($stmt_user) {
    $stmt_user->bind_param("s", $帳號);
    $stmt_user->execute();
    $stmt_user->bind_result($user_id);
    $stmt_user->fetch();
    $stmt_user->close();

    if (!$user_id) {
        echo "無法找到對應的 user_id，請重新登入";
        exit();
    }
} else {
    echo "查詢 user_id 時出現錯誤: " . $link->error;
    exit();
}

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得表單資料
    $name = $_POST['username'];
    $gender = ($_POST['gender'] == '男') ? 1 : 2; // 根據性別選擇相應的 gender_id
    $birthday = $_POST['userdate'];
    $idcard = $_POST['useridcard'];
    $phone = $_POST['userphone'];
    $email = $_POST['useremail'];

    // 處理圖片上傳
    $imagePath = null;
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == UPLOAD_ERR_OK) {
        // 設定圖片保存的目錄
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // 如果目錄不存在，創建它
        }

        // 生成唯一的文件名，避免重名
        $imagePath = $uploadDir . uniqid() . "_" . basename($_FILES['profilePicture']['name']);
        if (!move_uploaded_file($_FILES['profilePicture']['tmp_name'], $imagePath)) {
            echo "圖片上傳失敗";
            exit();
        }
    }

    // 檢查 profession 表中是否已經存在該 user_id 的資料
    $sql_check = "SELECT COUNT(*) FROM profession WHERE user_id = ?";
    $stmt_check = $link->prepare($sql_check);
    if ($stmt_check) {
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            // 更新 profession 表中的資料
            $sql_update = "UPDATE profession SET gender_id = ?, birthday = ?, idcard = ?, phone = ?, email = ?, image = ? WHERE user_id = ?";
            $stmt_update = $link->prepare($sql_update);
            if ($stmt_update) {
                $stmt_update->bind_param("isssssi", $gender, $birthday, $idcard, $phone, $email, $imagePath, $user_id);
                if ($stmt_update->execute()) {
                    // 資料更新成功後跳轉到 n_profile.php
                    echo "<script>alert('資料更新成功'); window.location.href = 'n_profile.php';</script>";
                    exit();
                } else {
                    echo "資料更新失敗: " . $stmt_update->error;
                }
                $stmt_update->close();
            } else {
                echo "更新預備語句失敗: " . $link->error;
            }
        } else {
            // 插入 profession 表中的新資料
            $sql_insert = "INSERT INTO profession (user_id, gender_id, birthday, idcard, phone, email, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $link->prepare($sql_insert);
            if ($stmt_insert) {
                $stmt_insert->bind_param("iisssss", $user_id, $gender, $birthday, $idcard, $phone, $email, $imagePath);
                if ($stmt_insert->execute()) {
                    // 資料新增成功後跳轉到 n_profile.php
                    echo "<script>alert('資料新增成功'); window.location.href = 'n_profile.php';</script>";
                    exit();
                } else {
                    echo "資料新增失敗: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            } else {
                echo "插入預備語句失敗: " . $link->error;
            }
        }
    } else {
        echo "檢查 profession 表時出現錯誤: " . $link->error;
    }
}

// 關閉連線
$link->close();
?>
