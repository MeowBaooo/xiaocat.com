<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增飲食紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php
session_start();
include 'pdo.php';
include 'user_header.php';
$sql="select * from pets where user_id=?";
$stmt=$conn->prepare($sql);
$stmt->execute([$_SESSION["user_id"]]);
$pets=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
    <div class="container mt-4">
        <h2 class="mb-4 text-primary">🍽 新增飲食紀錄</h2>
        <form action="api.php?do=add_food_log" method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">寵物</label>
                <select name="pet_id" class="form-select" required>
                    <?php foreach ($pets as $pet): ?>
                       <!-- <option value="<?= $pet['id'] ?>"><?= $pet['name'] ?></option>-->
                        <option value="<?= $pet['id'] ?>" <?= isset($_GET['pet_id']) && $_GET['pet_id'] == $pet['id'] ? 'selected' : '' ?>>
    <?= $pet['name'] ?>
</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">日期</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">時間</label>
                <select name="meal_time" class="form-select">
                    <option>早餐</option>
                    <option>午餐</option>
                    <option>晚餐</option>
                    <option>點心</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">食物名稱</label>
                <input type="text" name="food_name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">份量</label>
                <input type="text" name="qty" class="form-control"> 
            </div>

            <div class="col-12">
                <label class="form-label">備註</label>
                <textarea name="notes" class="form-control" rows="2" ></textarea>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-success">✅ 新增紀錄</button>
                <a href="food_logs_list.php" class="btn btn-secondary">返回列表</a>
            </div>

        </form>
    </div>

</body>

</html>