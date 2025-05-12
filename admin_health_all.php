<?php
session_start();
include 'pdo.php';
include 'admin_header.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    echo "<script>alert('只有醫護人員可以查看全部健康紀錄');location.href='login.php'</script>";
    exit;
}

$sql = "SELECT h.id,h.date, h.item_type, h.value, h.notes, 
               p.name AS pet_name, 
               u.username AS user_name 
        FROM health_logs h
        JOIN pets p ON h.pet_id = p.id
        JOIN user u ON p.user_id = u.id
        ORDER BY h.date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>所有毛孩的健康紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">
    <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="text-primary mb-0">📋 全部毛孩健康紀錄</h3>
    <div class="btn-group" role="group">
        <a href="admin_health_add.php" class="btn btn-outline-success">➕ 新增健康紀錄</a>
        <a href="admin_dashboard.php" class="btn btn-outline-primary">🏠 返回後台首頁</a>
    </div>
</div>
        <?php if (empty($logs)): ?>
            <div class="alert alert-info">目前尚無任何健康紀錄</div>
        <?php else : ?>
            <table class="table table-bordered table-striped">
                <thead class="table-lignt">
                    <tr>
                        <th>日期</th>
                        <th>寵物名稱</th>
                        <th>飼主帳號</th>
                        <th>類型</th>
                        <th>數值</th>
                        <th>備註</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($logs as $log): ?>
    <tr>
        <td><?= $log['date'] ?></td>
        <td><?= $log['pet_name'] ?></td>
        <td><?= $log['user_name'] ?></td>
        <td><?= $log['item_type'] ?></td>
        <td><?= $log['value'] ?></td>
        <td><?= nl2br($log['notes']) ?></td>
        <td>
            <a href="admin_health_edit.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-warning">編輯</a>
            </a>
            <form action="api.php?do=admin_delete_health" method="post" style="display:inline;" onsubmit="return confirm('確定要刪除這筆紀錄嗎？')">
                <input type="hidden" name="id" value="<?= $log['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">🗑️ 刪除</button>
 </form> </td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
    </div>
</body>


</html>


            
      

