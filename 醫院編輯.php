<?php
session_start(); // 啟用 Session，用來管理使用者的登入資訊
include 'db.php'; // 引入資料庫連線檔案，通常內含 $link 變數作為資料庫連線

if ($_SERVER["REQUEST_METHOD"] == "POST") { // 檢查請求方法是否為 POST
    $user_id = $_POST['user_id']; // 從 POST 請求中獲取要更新的使用者 ID
    $field = $_POST['field']; // 從 POST 請求中獲取要更新的欄位名稱
    $value = $_POST['value']; // 從 POST 請求中獲取新的值

    // 更新使用者等級
    if ($field === 'grade_id') {
        // 檢查 grade_id 是否為允許的值 (1=使用者, 2=醫生, 3=護士)
        if (!in_array($value, [1, 2, 3])) {
            echo "無效的等級選擇"; // 輸出錯誤訊息
            exit; // 停止腳本執行
        }

        // 更新 user 表中的 grade_id
        $sql = "UPDATE user SET grade_id = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($link, $sql); // 預處理 SQL 查詢
        mysqli_stmt_bind_param($stmt, "ii", $value, $user_id); // 綁定參數，"ii" 表示兩個整數型別的參數

        if (mysqli_stmt_execute($stmt)) { // 執行預處理語句
            echo "更新成功"; // 如果更新成功，輸出成功訊息
        } else {
            echo "更新失敗: " . mysqli_error($link); // 如果更新失敗，輸出錯誤訊息，並附加資料庫錯誤詳細訊息
        }

        mysqli_stmt_close($stmt); // 關閉預處理語句
    }
    // 更新科別
    elseif ($field === 'department_id') {
        // 檢查 department_id 是否存在於 department 資料表中
        $check_sql = "SELECT department_id FROM department WHERE department_id = ?";
        $check_stmt = mysqli_prepare($link, $check_sql); // 預處理查詢
        mysqli_stmt_bind_param($check_stmt, "i", $value); // 綁定 department_id 參數
        mysqli_stmt_execute($check_stmt); // 執行查詢
        mysqli_stmt_store_result($check_stmt); // 儲存結果

        if (mysqli_stmt_num_rows($check_stmt) === 0) { // 如果沒有找到符合的 department_id
            echo "無效的科別選擇"; // 輸出錯誤訊息
            exit; // 停止腳本執行
        }

        mysqli_stmt_close($check_stmt); // 關閉檢查語句

        // 檢查 medical 表中是否存在該 user_id
        $check_medical_sql = "SELECT user_id FROM medical WHERE user_id = ?";
        $check_medical_stmt = mysqli_prepare($link, $check_medical_sql); // 預處理查詢
        mysqli_stmt_bind_param($check_medical_stmt, "i", $user_id); // 綁定 user_id 參數
        mysqli_stmt_execute($check_medical_stmt); // 執行查詢
        mysqli_stmt_store_result($check_medical_stmt); // 儲存結果

        if (mysqli_stmt_num_rows($check_medical_stmt) === 0) { // 如果 medical 表中沒有該 user_id
            // 插入一條新的記錄
            $insert_sql = "INSERT INTO medical (user_id, department_id) VALUES (?, ?)";
            $insert_stmt = mysqli_prepare($link, $insert_sql); // 預處理插入語句
            mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $value); // 綁定參數

            if (mysqli_stmt_execute($insert_stmt)) { // 執行插入語句
                echo "新增並更新成功"; // 如果插入成功，輸出成功訊息
            } else {
                echo "新增失敗: " . mysqli_error($link); // 如果插入失敗，輸出錯誤訊息，並附加資料庫錯誤詳細訊息
            }

            mysqli_stmt_close($insert_stmt); // 關閉插入語句
        } else {
            // 如果已存在，則更新 medical 表中的 department_id
            $sql = "UPDATE medical SET department_id = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($link, $sql); // 預處理 SQL 查詢
            mysqli_stmt_bind_param($stmt, "ii", $value, $user_id); // 綁定參數，"ii" 表示兩個整數型別的參數

            if (mysqli_stmt_execute($stmt)) { // 執行預處理語句
                echo "更新成功"; // 如果更新成功，輸出成功訊息
            } else {
                echo "更新失敗: " . mysqli_error($link); // 如果更新失敗，輸出錯誤訊息，並附加資料庫錯誤詳細訊息
            }

            mysqli_stmt_close($stmt); // 關閉預處理語句
        }

        mysqli_stmt_close($check_medical_stmt); // 關閉 medical 檢查語句
    } else {
        echo "無效的更新欄位"; // 如果欄位名稱不合法，輸出錯誤訊息
    }

    mysqli_close($link); // 關閉資料庫連線
}
?>
