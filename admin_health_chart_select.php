<?php
session_start();
include 'pdo.php';
include 'admin_header.php';
if (!isset($_SESSION["user_id"]) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('只有醫護人員可以使用此功能');location.href='login.php';</script>";
    exit;
}

$sql = "select p.id,p.name,u.username
      from pets p
      join user u on p.user_id = u.id
      order by p.name";
$stmt = $conn->prepare($sql);
$stmt->execute(); // ⬅️ 這行不能少！
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>選擇毛孩查看圖表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">📈 選擇毛孩查看體重圖表</h4>
            </div>
            <div class="card-body">
                <form method="get" action="health_chart.php">
                    <div class="mb-3">
                        <label class="form-label">選擇毛孩</label>
                        <select name="pet_id" class="form-select" required>
                            <?php foreach ($pets as $pet): ?>
                                <option value="<?= $pet['id'] ?>">
                                    <?= $pet['name'] ?>(飼主:<?= $pet['username'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-info">查看圖表</button>
                         <a href="admin_dashboard.php" class="btn btn-secondary">返回後台</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>