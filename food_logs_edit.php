<?php
session_start();
include 'pdo.php';
include 'user_header.php';
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "缺少id";
}

$sql = "select * from food_logs where id=?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$log = $stmt->fetch();

if (!$log) {
    echo "找不到這筆紀錄";
}

$sql = "select * from pets where user_id=?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯飲食紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2 class="mb-4 text warning">📝 編輯飲食紀錄</h2>

        <form action="api.php?do=update_food_log" method="post" class="row g-3">
            <input type="hidden" name="id" value="<?= $log['id'] ?>">

            <div class="col-md-6">
                <label class="form-label">寵物</label>
                <select name="pet_id" class="form-select" required>
                    <?php foreach ($pets as $pet): ?>
                        <option value="<?= $pet['id'] ?>" <?= $pet['id'] == $log['pet_id'] ? 'selected' : '' ?>>
                            <?= $pet["name"] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">日期</label>
                <input type="date" name="date" class="form-control" value="<?= $log['date'] ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">時間</label>
                <select name="meal_time" class="form-select">
                    <?php foreach (["早餐", "午餐", "晚餐", "點心"] as $time): ?>
                        <option value="<?= $time ?>" <?= $log['meal_time'] == $time ? 'selected' : '' ?>>
                            <?= $time ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">食物名稱</label>
                <input type="text" name="food_name" class="form-control" value="<?= $log['food_name'] ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">份量</label>
                <input type="text" name="qty" class="form-control" value="<?= $log['qty'] ?>">
            </div>

            <div class="col-md-12">
                <label class="form-label">備註</label>
                <textarea name="notes" class="form-control" rows="2"><?= $log['notes'] ?></textarea>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-warning">💾 更新紀錄</button>
                        <a href="food_logs_list.php" class="btn btn-secondary">返回列表</a>
            </div>
        </form>
    </div>
</body>

</html>