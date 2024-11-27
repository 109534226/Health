<?php
// 開始 session（如果需要用到 session）
session_start();

// 引入資料庫連線檔案
include 'db.php'; // 假設資料庫連線的檔案名稱為 db.php

// 檢查表單是否提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 取得表單資料
  $id = intval($_POST['id']); // 隱藏欄位傳遞的 ID
  $appointment_date = $_POST['appointment_date'];
  $consultationt = $_POST['consultationt']; // '早', '午', or '晚'
  $clinic_number = $_POST['clinic_number'];
  $patient_name = $_POST['patient_name'];
  $birth_date = $_POST['birth_date'];
  $gender = $_POST['gender']; // '男' or '女'
  $department = $_POST['department'];
  $doctor_name = $_POST['doctor_name'];
  $doctor_advice = $_POST['doctor_advice'];

  // 將性別轉換為對應的 gender_id
  $gender_id = ($gender === '男') ? 1 : (($gender === '女') ? 2 : null);

  try {
    // 查詢 consultationT_id
    $consultationT_id_query = "SELECT consultationT_id FROM consultationt WHERE consultationT = ?";
    $stmt1 = $link->prepare($consultationT_id_query);
    $stmt1->bind_param('s', $consultationt);
    $stmt1->execute();
    $stmt1->bind_result($consultationT_id);
    $stmt1->fetch();
    $stmt1->close();

    if (!$consultationT_id) {
      throw new Exception("無效的看診時段！");
    }

    // 更新 `patient` 資料表
    $update_patient_sql = "
            UPDATE patient
            SET
                patientname = ?,
                gender_id = ?,
                birthday = ?,
                medicalnumber = ?,
                doctoradvice = ?
            WHERE patient_id = ?
        ";
    $stmt = $link->prepare($update_patient_sql);
    $stmt->bind_param(
      'sisssi',
      $patient_name,
      $gender_id,
      $birth_date,
      $clinic_number,
      $doctor_advice,
      $id
    );
    $stmt->execute();

    // 確保更新成功
    if ($stmt->affected_rows > 0) {
      // 更新成功，顯示 alert 並跳轉
      echo "<script>
            alert('修改成功！');
            window.location.href = 'd_advicesee.php';
          </script>";
      exit();
    } else {
      // 若沒有受影響的列，可能是資料未修改或錯誤
      echo "<script>
            alert('沒有任何變更，請確認資料是否正確。');
            window.location.href = 'd_advicesee.php';
          </script>";
      exit();
    }
    // 確保更新成功
    // if ($stmt->affected_rows > 0) {
    //     // 更新 `doctorshift` 資料表
    //     $update_doctorshift_sql = "
    //         UPDATE doctorshift
    //         SET
    //             consultationD = ?,
    //             consultationT_id = ?,
    //             clinicnumber_id = (SELECT clinicnumber_id FROM clinicnumber WHERE clinic_number = ? LIMIT 1)
    //         WHERE doctorshift_id = (SELECT doctorshift_id FROM patient WHERE patient_id = ?)
    //     ";
    //     $stmt2 = $link->prepare($update_doctorshift_sql);
    //     $stmt2->bind_param(
    //         'sisi',
    //         $appointment_date,
    //         $consultationT_id,
    //         $clinic_number,
    //         $id
    //     );
    //     $stmt2->execute();

    //     if ($stmt2->affected_rows > 0) {
    //         echo "更新成功！";
    //     } else {
    //         echo "更新班表資料失敗，請確認資料正確性。";
    //     }
    // } else {
    //     echo "更新患者資料失敗，請確認資料正確性。";
    // }
  } catch (Exception $e) {
    // 捕獲異常並顯示錯誤訊息
    echo "錯誤: " . $e->getMessage();
  }
  $link->close();
}
?>