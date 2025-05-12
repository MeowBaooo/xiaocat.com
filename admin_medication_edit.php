<?php
session_start();
include 'pdo.php';
include 'admin_header.php';
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('你沒有權限');location.href='login.php';</script>";
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('缺少紀錄ID');location.href='admin_medication_all.php';</script>";
    exit;
}

$sql = "select * from medications where id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<script>alert('資料不存在');location.href='admin_medication_all.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST["date"];
    $medication_name = $_POST["medication_name"];
    $dosage = $_POST["dosage"];
    $frequency = $_POST["frequency"];
    $notes = $_POST["notes"];

    $sql = "update medications set date = ?,medication_name=?,dosage=?,frequency = ?,notes=? where id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$date, $medication_name, $dosage, $frequency, $notes, $id]);

    echo "<script>alert('紀錄已更新');location.href='admin_medication_all.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯用藥紀錄</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h3 class="text-primary mb-4">✏️ 編輯用藥紀錄</h3>
        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label">用藥日期</label>
                <input type="date" name="date" class="form-control" value="<?= $data['date'] ?>" required  max="<?= date('Y-m-d') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">藥品名稱</label>
                <input type="text" name="medication_name" class="form-control" value="<?= $data['medication_name'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">劑量</label>
                <input type="text" name="dosage" class="form-control" value="<?= $data['dosage'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">頻率</label>
                <input type="text" name="frequency" class="form-control" value="<?= $data['frequency'] ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">備註</label>
                <textarea name="notes" class="form-control" rows="3"><?= $data['notes'] ?></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="admin_medication_all.php" class="btn btn-outline-secondary">返回列表</a>
                <button type="submit" class="btn btn-success">💾 儲存變更</button>
            </div>
        </form>
    </div>
</body>

</html>