<?php
session_start();
include 'pdo.php';
include 'user_header.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user'){
    echo "<script>alert('請先登入');location.href='login.php';</script>";
    exit;
}

$pet_id = $_GET['pet_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$pet_id) {
    echo "<script>alert('缺少毛孩');location.href='pets.php';</script>";
    exit;
}

// 驗證毛孩是否屬於這個使用者
$sql = "SELECT * FROM pets WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$pet_id, $user_id]);
$pet = $stmt->fetch();

if (!$pet) {
    echo "<script>alert('這不是你的毛孩！');location.href='pets.php';</script>";
    exit;
}

// 抓用藥紀錄
$sql = "SELECT * FROM medications WHERE pet_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$pet_id]);
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pet['name']) ?> 的用藥紀錄</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="text-primary mb-3">💊 <?= htmlspecialchars($pet['name']) ?> 的用藥紀錄</h3>
    <a href="pets.php" class="btn btn-secondary mb-3">返回我的毛孩列表</a>

    <?php if (count($logs) === 0): ?>
        <div class="alert alert-info">目前沒有任何用藥紀錄</div>
    <?php else: ?>
        <table class="table table-bordered table-striped bg-white shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th>用藥日期</th>
                    <th>藥品名稱</th>
                    <th>劑量</th>
                    <th>頻率</th>
                    <th>備註</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['date']) ?></td>
                        <td><?= htmlspecialchars($log['medication_name']) ?></td>
                        <td><?= htmlspecialchars($log['dosage']) ?></td>
                        <td><?= htmlspecialchars($log['frequency']) ?></td>
                        <td><?= nl2br(htmlspecialchars($log['notes'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
