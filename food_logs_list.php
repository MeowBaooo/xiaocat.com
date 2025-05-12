<?php
include 'pdo.php';
include 'user_header.php';
session_start();

if(isset($_GET["pet_id"])){

    $pet_id = $_GET["pet_id"];
    $sql = "SELECT f.*, p.name AS pet_name
            FROM food_logs f
            JOIN pets p ON f.pet_id = p.id
            WHERE f.pet_id = ?
            ORDER BY f.date DESC, f.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$pet_id]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}else{
$sql="select f.* ,p.name as pet_name
      from food_logs f
      join pets p on f.pet_id =p.id
      where p.user_id=?
      order by f.date desc,f.id desc";
$stmt=$conn->prepare($sql);
$stmt->execute([$_SESSION["user_id"]]);
$logs=$stmt->fetchAll(PDO::FETCH_ASSOC);    
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>飲食紀錄列表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4 text-primary">📋 飲食紀錄列表</h2>

        <?php if(count($logs)>0): ?>
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>寵物</th>
                        <th>日期</th>
                        <th>時間</th>
                        <th>食物</th>
                        <th>份量</th>
                        <th>備註</th>
                        <th>新增時間</th>
                        <th>編輯</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $log): ?>
                        <tr>
                            <td><?=$log['pet_name'] ?></td>
                            <td><?=$log['date'] ?></td>
                            <td><?=$log['meal_time'] ?></td>
                            <td><?=$log['food_name'] ?></td>
                            <td><?=$log['qty'] ?></td>
                            <td><?=$log['notes'] ?></td>
                            <td><?=$log['created_at'] ?></td>
                            <td>
                            <a href="food_logs_edit.php?id=<?=$log['id']?>" class="btn btn-sm btn-warning">📝 編輯</a>
                            <a href="api.php?do=delete_food_log&id=<?=$log['id']?>"  class="btn btn-sm btn-danger" onclick="return confirm('確定要刪除嗎？')">🗑️ 刪除</a>
     
                        </td>
                        </tr>
                        <?php endforeach;?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="alert alert-warning">目前尚無飲食紀錄唷～</div>
                <?php endif; ?>
                <a href="food_logs.php<?= isset($pet_id) ? '?pet_id=' . $pet_id : '' ?>" class="btn btn-success">➕ 新增飲食紀錄</a>
                <a href="pets.php" class="btn btn-outline-secondary">🔙 返回我的毛孩</a>
            </div>
    
   
</body>
</html>