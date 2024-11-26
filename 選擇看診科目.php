<?php
// 檢查是否為 POST 請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic = trim($_POST['clinic'] ?? ''); // 診所名稱

    // 驗證請求參數
    if (empty($clinic)) {
        echo "<option value=\"\">無可用科目</option>";
        exit;
    }

    // 引入資料庫連線
    include 'db.php';

    // 查詢對應的醫院 ID
    $query = "SELECT hospital_id FROM hospital WHERE hospital = ?";
    $stmt = $link->prepare($query);

    if (!$stmt) {
        echo "<option value=\"\">伺服器錯誤，請稍後再試。</option>";
        exit;
    }

    $stmt->bind_param('s', $clinic);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $hospital = $result->fetch_assoc();
        $hospital_id = $hospital['hospital_id'];

        // 使用 hospital_id 查詢科別
        $dept_query = "SELECT department FROM department WHERE hospital_id = ?";
        $dept_stmt = $link->prepare($dept_query);

        if (!$dept_stmt) {
            echo "<option value=\"\">伺服器錯誤，請稍後再試。</option>";
            exit;
        }

        $dept_stmt->bind_param('i', $hospital_id);
        $dept_stmt->execute();
        $dept_result = $dept_stmt->get_result();

        if ($dept_result->num_rows > 0) {
            while ($dept_row = $dept_result->fetch_assoc()) {
                echo "<option value=\"" . htmlspecialchars($dept_row['department'], ENT_QUOTES, 'UTF-8') . "\">" . htmlspecialchars($dept_row['department'], ENT_QUOTES, 'UTF-8') . "</option>";
            }
        } else {
            echo "<option value=\"\">無可用科目</option>";
        }

        $dept_stmt->close();
    } else {
        echo "<option value=\"\">無可用科目</option>";
    }

    // 關閉連線
    $stmt->close();
    $link->close();
}
?>
