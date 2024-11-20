<?php
include 'db.php'; // 引入資料庫連線

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clinic'])) {
    $clinic = $_POST['clinic'];

    // 查詢對應診所的科別
    $sql = "SELECT 科別 FROM hospital WHERE 醫事機構 = '" . mysqli_real_escape_string($link, $clinic) . "'";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) > 0) {
        $departments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // 將科別以頓號分隔轉成陣列
            $departments = array_merge($departments, explode("、", $row['科別']));
        }

        // 生成下拉選單的 <option>
        foreach ($departments as $department) {
            echo "<option value='" . htmlspecialchars($department, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($department, ENT_QUOTES, 'UTF-8') . "</option>";
        }
    } else {
        echo "<option value='' disabled>無可用科目</option>";
    }

    mysqli_close($link);
}
?>
