<?php
session_start();
include "db.php"; // 確保已經連接到資料庫

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 獲取使用者帳號
    $帳號 = $_SESSION["帳號"];

    // 檢查使用者帳號是否存在
    if (empty($帳號)) {
        echo "無法獲取使用者帳號，請確認是否已正確登入";
        exit;
    }

    // 透過帳號查詢 user_id
    $userQuery = "SELECT `user_id` FROM `user` WHERE `account` = ?";
    if ($userStmt = $link->prepare($userQuery)) {
        $userStmt->bind_param('s', $帳號); // 使用帳號綁定參數
        $userStmt->execute(); // 執行查詢
        $userStmt->store_result(); // 確保結果被保存
        $userStmt->bind_result($user_id); // 綁定查詢結果到變數
        $userStmt->fetch(); // 獲取查詢結果

        // 檢查是否找到對應的 user_id
        if (empty($user_id)) {
            echo "找不到對應的使用者帳號";
            exit;
        }

        $userStmt->close(); // 關閉語句
    } else {
        echo "查詢使用者資料失敗：" . $link->error;
        exit;
    }

    // 獲取醫療診所和科別以查找 department_id
    $selectedClinic = $_POST['hospital']; // 取得表單提交的醫療診所
    $selectedDepartment = $_POST['department']; // 取得表單提交的科別
    
    $departmentQuery = "SELECT `department_id` FROM `department` d JOIN `hospital` h ON d.`hospital_id` = h.`hospital_id` WHERE h.`hospital` = ? AND d.`department` = ?";
    if ($deptStmt = $link->prepare($departmentQuery)) {
        $deptStmt->bind_param('ss', $selectedClinic, $selectedDepartment); // 使用醫療診所和科別綁定參數
        $deptStmt->execute(); // 執行查詢
        $deptStmt->store_result(); // 確保結果被保存
        $deptStmt->bind_result($department_id); // 綁定查詢結果到變數
        $deptStmt->fetch(); // 獲取查詢結果

        // 檢查是否找到對應的 department_id
        if (empty($department_id)) {
            echo "找不到對應的醫療診所和科別";
            exit;
        }

        $deptStmt->close(); // 關閉語句
    } else {
        echo "查詢科別資料失敗：" . $link->error;
        exit;
    }

    // 獲取看診日期和看診時間以查找 doctorshift_id
    $consultationDate = isset($_POST['date']) ? htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8') : ''; // 取得表單提交的看診日期
    $consultationTime = isset($_POST['consultationtime']) ? htmlspecialchars($_POST['consultationtime'], ENT_QUOTES, 'UTF-8') : ''; // 取得表單提交的看診時間

    // 將看診時間轉換為對應的數值
    switch ($consultationTime) {
        case '上午診':
            $consultationTime = 1;
            break;
        case '下午診':
            $consultationTime = 2;
            break;
        case '晚上診':
            $consultationTime = 3;
            break;
        default:
            echo "無效的看診時間";
            exit;
    }
    
    // 檢查看診日期和時間是否為空
    if (empty($consultationDate) || empty($consultationTime)) {
        echo "無法獲取看診日期或時間，請確認是否已正確填寫";
        exit;
    }

    // 查詢對應的 doctorshift_id
    $doctorshiftQuery = "SELECT `doctorshift_id` FROM `doctorshift` WHERE `consultationD` = ? AND `consultationT_id` = ?";
    if ($doctorshiftStmt = $link->prepare($doctorshiftQuery)) {
        $doctorshiftStmt->bind_param('si', $consultationDate, $consultationTime); // 使用看診日期和時間綁定參數
        $doctorshiftStmt->execute(); // 執行查詢
        $doctorshiftStmt->store_result(); // 確保結果被保存
        $doctorshiftStmt->bind_result($doctorshift_id); // 綁定查詢結果到變數
        $doctorshiftStmt->fetch(); // 獲取查詢結果

        // 檢查是否找到對應的 doctorshift_id
        if (empty($doctorshift_id)) {
            echo "找不到對應的看診資料";
            exit;
        }

        $doctorshiftStmt->close(); // 關閉語句
    } else {
        echo "查詢看診資料失敗：" . $link->error;
        exit;
    }

    // 取得表單中提交的患者資料
    $patientname = $_POST['patientname']; // 取得患者姓名
    $gender = $_POST['gender'] == '男' ? 1 : 2; // 假設 1 為男，2 為女
    $birthday = $_POST['birthday']; // 取得患者生日
    $idcard = $_POST['idcard']; // 取得患者身份證號碼
    $phone = $_POST['phone']; // 取得患者聯絡電話
    $address = $_POST['address']; // 取得患者住址
    $currentsymptoms = $_POST['current']; // 取得患者目前症狀
    $allergies = $_POST['allergies']; // 取得患者過敏史
    $medicalhistory = $_POST['medicalhistory']; // 取得患者病史

    // 插入患者資料到 patient 表格
    $insertPatientSQL = "INSERT INTO `patient` (`user_id`, `patientname`, `gender_id`, `birthday`, 
    `idcard`, `phone`, `address`, `currentsymptoms`, `allergies`, `medicalhistory`, `department_id`, 
    `doctorshift_id`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = $link->prepare($insertPatientSQL)) {
        $stmt->bind_param('isississssii', $user_id, $patientname, $gender, $birthday, $idcard, $phone, $address, $currentsymptoms, $allergies, $medicalhistory, $department_id, $doctorshift_id); // 綁定所有插入參數
        if ($stmt->execute()) { // 執行插入操作
            // 插入成功後，更新 doctorshift 的 reserve 欄位（人數加 1）
            // echo $consultationDate;
            // echo $consultationTime;
            $updateDoctorshiftSQL = "UPDATE `doctorshift` SET `reserve` = `reserve` + 1 WHERE `consultationD` = ? AND `consultationT_id` = ?";
            if ($updateStmt = $link->prepare($updateDoctorshiftSQL)) {
                $updateStmt->bind_param('si', $consultationDate, $consultationTime); // 使用看診日期和時間綁定參數
                $updateStmt->execute(); // 執行更新操作
                echo "<script>alert('預約成功！'); window.location.href='u_reserve.php';</script>"; // 顯示預約成功彈跳視窗並跳轉頁面
                exit();
            } else {
                echo "更新看診預約人數失敗：" . $link->error; // 顯示更新預約人數失敗的錯誤訊息
            }
        } else {
            echo "新增患者資料失敗：" . $stmt->error; // 顯示插入患者資料失敗的錯誤訊息
        }
        $stmt->close(); // 關閉語句
    } else {
        echo "準備新增患者資料的語句失敗：" . $link->error; // 顯示準備插入語句失敗的錯誤訊息
    }
}
?>
