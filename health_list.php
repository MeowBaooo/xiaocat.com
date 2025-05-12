<?php
session_start();
include "pdo.php";
include 'user_header.php';
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$pet_id = $_GET["pet_id"] ?? null;
if (!$pet_id) {
    echo "請提供毛孩ID";
    exit;
}

// ✅ 抓毛孩資料（限制只能看自己的）
$sql = "select * from pets where id =? and user_id =?";
$stmt = $conn->prepare($sql);
$stmt->execute([$pet_id, $_SESSION["user_id"]]);
$pet = $stmt->fetch();

if (!$pet) {
    echo "找不到毛孩或無權限";
    exit;
}

$sql = "select * from health_logs where pet_id =? order by date desc";
$stmt = $conn->prepare($sql);
$stmt->execute([$pet_id]);
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pet["name"] ?>的健康紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mg-3">
            <h3 class="text-success">🩺<?= $pet['name'] ?>的健康紀錄</h3>
            <div class="d-flex gap-2">
                <a href='health_add.php?pet_id=<?= $pet['id'] ?>' class="btn btn-success">➕ 新增健康紀錄</a>
                <a href='health_chart.php?pet_id=<?= $pet['id'] ?>' class="btn btn-outline-primary">📈 體重圖表</a>
            </div>
        </div>

        <?php if (empty($logs)): ?>
            <div class="alert alert-info">目前沒有任何健康紀錄。</div>
        <?php else : ?>
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                <tr>
                    <th>日期</th>
                    <th>類型</th>
                    <th>數值</th>
                    <th>備註</th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($logs as $log): ?>
    <tr>
        <td><?= $log['date'] ?></td>
        <td><?= $log['item_type'] ?></td>
        <td><?= $log['value'] ?></td>
        <td><?= nl2br($log['notes']) ?></td>
       
    </tr>
<?php endforeach; ?>
            </tbody>
            </table>
            <?php endif; ?>

            <div class="mt-3">
                <a href="pets.php" class="btn btn-secondary">返回毛孩清單</a>
            </div>
    </div>
</body>

</html>


