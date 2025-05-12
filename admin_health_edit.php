<?php
session_start();
include "pdo.php";
include 'admin_header.php';
//echo "目前登入身分是：" . ($_SESSION["role"] ?? "未設定") . "<br>";
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('只有醫護人員可以編輯紀錄');location.href='login.php';</script>";
    exit;
}

$id = $_GET["id"] ?? null;
if (!$id) {
    echo "<script>alert('請提供紀錄 ID');location.href='admin_health_all.php';</script>";
    exit;
}
// 取得紀錄資料
$sql = "SELECT h.*, p.name AS pet_name 
        FROM health_logs h
        JOIN pets p ON h.pet_id = p.id
        WHERE h.id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$log = $stmt->fetch();
//var_dump($log);
//exit;
if (!$log) {
    echo "<script>alert('找不到紀錄');location.href='admin_health_all.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯健康紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0">✏️編輯 <?= $log['pet_name'] ?> 的健康紀錄</h4>
            </div>
            <div class="card-body">
                <form action="api.php?do=admin_update_health" method="post">
                    <input type="hidden" name="id" value="<?= $log['id'] ?>">
                   <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">紀錄日期</label>
                        <input type="date" name="date" class="form-control" value="<?= $log['date'] ?>" required  max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">類型</label>
                        <select name="type" class="form-select">
                            <option value="體重" <?= $log["item_type"] === '體重' ? 'selected' : '' ?>>體重</option>
                            <option value="疫苗" <?= $log["item_type"] === '疫苗' ? 'selected' : '' ?>>疫苗</option>
                            <option value="看診" <?= $log["item_type"] === '看診' ? 'selected' : '' ?>>看診</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">數值（如kg、mL）</label>
                        <input type="text" name="value" class="form-control" value="<?= $log['value'] ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">備註</label>
                        <textarea name="notes" rows='4' class="form-control"><?= $log["notes"] ?></textarea>
                    </div>
            </div>
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-warning">儲存變更</button>
                <a href="admin_health_all.php" class="btn btn-secondary">返回清單</a>
            </div>
            </form>
        </div>
        </div>

   
</div>
</body>

</html>