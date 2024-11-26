<?php
include 'db.php'; // 引入資料庫連接

// 確認是否通過 POST 方法接收到所有必要的資料
$patientName = isset($_POST['patientname']) ? mysqli_real_escape_string($link, $_POST['patientname']) : '';
$idCard = isset($_POST['idcard']) ? mysqli_real_escape_string($link, $_POST['idcard']) : '';
$birthday = isset($_POST['birthday']) ? mysqli_real_escape_string($link, $_POST['birthday']) : '';
$phone = isset($_POST['phone']) ? mysqli_real_escape_string($link, $_POST['phone']) : '';
$address = isset($_POST['address']) ? mysqli_real_escape_string($link, $_POST['address']) : '';
$currentSymptoms = isset($_POST['current']) ? mysqli_real_escape_string($link, $_POST['current']) : '';
$allergies = isset($_POST['allergies']) ? mysqli_real_escape_string($link, $_POST['allergies']) : '';
$medicalHistory = isset($_POST['medicalhistory']) ? mysqli_real_escape_string($link, $_POST['medicalhistory']) : '';

// 關聯的外鍵資料
$selectedClinic = isset($_POST['clinic']) ? mysqli_real_escape_string($link, $_POST['clinic']) : '';
$selectedDepartment = isset($_POST['department']) ? mysqli_real_escape_string($link, $_POST['department']) : '';
$doctorName = isset($_POST['doctor']) ? mysqli_real_escape_string($link, $_POST['doctor']) : '';
$timePeriod = isset($_POST['timePeriod']) ? mysqli_real_escape_string($link, $_POST['timePeriod']) : '';
$date = isset($_POST['date']) ? mysqli_real_escape_string($link, $_POST['date']) : '';

// 將接收到的 $timePeriod 轉換為資料庫中的字母（早、午、晚）
switch ($timePeriod) {
    case '上午':
    case '上午診':
        $timePeriod = '早';
        break;
    case '下午':
    case '下午診':
        $timePeriod = '午';
        break;
    case '晚上':
    case '晚上診':
        $timePeriod = '晚';
        break;
    default:
        echo "無效的看診時段。";
        exit;
}

// 獲取看診時段的 consultationT_id
$timePeriodQuery = "SELECT consultationT_id FROM consultationt WHERE consultationT = '$timePeriod'";
$timePeriodResult = mysqli_query($link, $timePeriodQuery);
if ($timePeriodResult && mysqli_num_rows($timePeriodResult) > 0) {
    $timePeriodData = mysqli_fetch_assoc($timePeriodResult);
    $consultationTId = $timePeriodData['consultationT_id'];

    // 獲取 hospital_id, department_id, user_id（醫生）和 doctorshift_id，並核對所有資料
    $query = "
        SELECT d.department_id, h.hospital_id, u.user_id, ds.doctorshift_id
        FROM doctorshift ds
        JOIN user u ON ds.user_id = u.user_id
        JOIN medical m ON ds.medical_id = m.medical_id
        JOIN department d ON m.department_id = d.department_id
        JOIN hospital h ON d.hospital_id = h.hospital_id
        WHERE u.name = '$doctorName'
        AND h.hospital = '$selectedClinic'
        AND d.department = '$selectedDepartment'
        AND ds.consultationD = '$date'
        AND ds.consultationT_id = '$consultationTId'
        LIMIT 1
    ";

    $result = mysqli_query($link, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $hospitalId = $data['hospital_id'];
        $departmentId = $data['department_id'];
        $doctorShiftId = $data['doctorshift_id'];

        // 插入患者資料到 patient 資料表
        $insertPatientQuery = "
            INSERT INTO patient (user_id, patientname, birthday, idcard, phone, address, currentsymptoms, allergies, medicalhistory, hospital_id, doctorshift_id, created_at)
            VALUES ('{$data['user_id']}', '$patientName', '$birthday', '$idCard', '$phone', '$address', '$currentSymptoms', '$allergies', '$medicalHistory', '$hospitalId', '$doctorShiftId', NOW())
        ";

        if (mysqli_query($link, $insertPatientQuery)) {
            // 插入成功，接著更新 doctorshift 表中的 reserve 欄位
            $updateReserveQuery = "
                UPDATE doctorshift
                SET reserve = IFNULL(reserve, 0) + 1
                WHERE doctorshift_id = '$doctorShiftId'
            ";

            if (mysqli_query($link, $updateReserveQuery)) {
                echo "預約成功！預約人數已更新。";
            } else {
                echo "患者資料儲存成功，但更新預約人數失敗：" . mysqli_error($link);
            }
        } else {
            echo "預約失敗，無法儲存患者資料：" . mysqli_error($link);
        }
    } else {
        echo "無法找到對應的醫療資料。請確認選擇的醫院、科別、醫生、日期和時段是否正確。";
    }
} else {
    echo "無法找到對應的看診時段，請確認選擇的看診時段是否正確。";
}

mysqli_close($link);
?>
