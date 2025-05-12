<?php

use function PHPSTORM_META\sql_injection_subst;

session_start();
include "pdo.php";
include 'admin_header.php';
$pet_id = $_GET["pet_id"] ?? "";

if (!isset($_SESSION["user_id"])) {
    header("Location:login.php");
    exit;
}

if ($_SESSION['role'] == 'admin') {
    $sql = "select p.*,u.username from pets p join user u on p.user_id =u.id";
    $stmt = $conn->query($sql);
} else {
    $sql = "select * from pets where user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION["user_id"]]);
}
$pets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增健康紀錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">➕ 醫護人員新增健康紀錄</h4>
            </div>
            <div class="card-body">
                <form action="api.php?do=admin_add_health" method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">毛孩</label>
                            <select name="pet_id" class="form-select" required>
                                <?php foreach ($pets as $pet): ?>
                                    <option value="<?= $pet['id'] ?>">
                                <?= htmlspecialchars($pet['name']) ?>（飼主：<?= htmlspecialchars($pet['username']) ?>）
                            </option>                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">記錄日期</label>
                            <input type="date" name="date" class="form-control" required  max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-class">類型</label>
                            <select name="type" class="form-select">
                                <option value="體重">體重</option>
                                <option value="疫苗">疫苗</option>
                                <option value="看診">看診</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">數值（如kg、mL）</label>
                            <input type="text" name="value" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-labe">備註</label>
                            <textarea name="notes" rows="4" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success">儲存紀錄</button>
                        <a href="admin_dashboard.php" class="btn btn-secondary">返回後台</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>