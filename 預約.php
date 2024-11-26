<?php
session_start();
include "db.php"; // 確保已經連接到資料庫

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 獲取使用者帳號
    $帳號 = $_SESSION["帳號"];

    if (empty($帳號)) {
        echo "無法獲取使用者帳號，請確認是否已正確登入";
        exit;
    }

    // 透過帳號查詢 user_id
    $userQuery = "SELECT `user_id` FROM `user` WHERE `account` = ?";
    if ($userStmt = $link->prepare($userQuery)) {
        $userStmt->bind_param('s', $帳號);
        $userStmt->execute();
        $userStmt->store_result(); // 確保結果被保存
        $userStmt->bind_result($user_id);
        $userStmt->fetch();

        if (empty($user_id)) {
            echo "找不到對應的使用者帳號";
            exit;
        }

        $userStmt->close();
    } else {
        echo "查詢使用者資料失敗：" . $link->error;
        exit;
    }

    // 獲取醫療診所和科別以查找 department_id
    $selectedClinic = $_POST['hospital'];
    $selectedDepartment = $_POST['department'];

    $departmentQuery = "SELECT `department_id` FROM `department` d JOIN `hospital` h ON d.`hospital_id` = h.`hospital_id` WHERE h.`hospital` = ? AND d.`department` = ?";
    if ($deptStmt = $link->prepare($departmentQuery)) {
        $deptStmt->bind_param('ss', $selectedClinic, $selectedDepartment);
        $deptStmt->execute();
        $deptStmt->store_result();
        $deptStmt->bind_result($department_id);
        $deptStmt->fetch();

        if (empty($department_id)) {
            echo "找不到對應的醫療診所和科別";
            exit;
        }

        $deptStmt->close();
    } else {
        echo "查詢科別資料失敗：" . $link->error;
        exit;
    }

    // 獲取看診日期和看診時間以查找 doctorshift_id
    $consultationDate = isset($_POST['date']) ? htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8') : '';
    // 將看診時間文字轉換為對應的 consultationT_id
    switch ($consultationTime) {
        case '上午診':
            $consultationTime = "早";
            break;
        case '下午診':
            $consultationTime = "午";
            break;
        case '晚上診':
            $consultationTime = "晚";
            break;
        default:
            echo "無效的看診時間";
            exit;
    }
    echo "日期: " . $consultationDate . "<br>";
    echo "時間: " . $consultationTime . "<br>";
    
    if (empty($consultationDate) || empty($consultationTime)) {
        echo "無法獲取看診日期或時間，請確認是否已正確填寫";
        exit;
    }

    $doctorshiftQuery = "SELECT `doctorshift_id` FROM `doctorshift` WHERE `consultationD` = ? AND `consultationT_id` = ?";
    if ($doctorshiftStmt = $link->prepare($doctorshiftQuery)) {
        $doctorshiftStmt->bind_param('si', $consultationDate, $consultationTime);
        $doctorshiftStmt->execute();
        $doctorshiftStmt->store_result();
        $doctorshiftStmt->bind_result($doctorshift_id);
        $doctorshiftStmt->fetch();

        if (empty($doctorshift_id)) {
            echo "找不到對應的看診資料";
            exit;
        }

        $doctorshiftStmt->close();
    } else {
        echo "查詢看診資料失敗：" . $link->error;
        exit;
    }

    // 取得表單中提交的資料
    $patientname = $_POST['patientname'];
    $gender = $_POST['gender'] == '男' ? 1 : 2; // 假設 1 為男，2 為女
    $birthday = $_POST['birthday'];
    $idcard = $_POST['idcard'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $currentsymptoms = $_POST['current'];
    $allergies = $_POST['allergies'];
    $medicalhistory = $_POST['medicalhistory'];

    // 插入患者資料到 patient 表格
    $insertPatientSQL = "INSERT INTO `patient` (`user_id`, `patientname`, `gender_id`, `birthday`, 
    `idcard`, `phone`, `address`, `currentsymptoms`, `allergies`, `medicalhistory`, `department_id`, 
    `doctorshift_id`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = $link->prepare($insertPatientSQL)) {
        $stmt->bind_param('isississssii', $user_id, $patientname, $gender, $birthday, $idcard, $phone, $address, $currentsymptoms, $allergies, $medicalhistory, $department_id, $doctorshift_id);
        if ($stmt->execute()) {
            // 插入成功後，更新 doctorshift 的 reserve 欄位（人數加 1）
            $updateDoctorshiftSQL = "UPDATE `doctorshift` SET `reserve` = `reserve` + 1 WHERE `doctorshift_id` = ?";
            if ($updateStmt = $link->prepare($updateDoctorshiftSQL)) {
                $updateStmt->bind_param('i', $doctorshift_id);
                $updateStmt->execute();
                echo "預約成功！";
                header('Location: u_reserve.php');
                exit();
            } else {
                echo "更新看診預約人數失敗：" . $link->error;
            }
        } else {
            echo "新增患者資料失敗：" . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "準備新增患者資料的語句失敗：" . $link->error;
    }
}
?>