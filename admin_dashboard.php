<?php
session_start();
include 'pdo.php';
include 'admin_header.php';
if ($_SESSION["role"] !== 'admin') {
    echo "<script>alert('非醫護人員，禁止進入');location.href='login.php';</script>";
    exit;
}

$username = $_SESSION['username'];

// 總毛孩數
$sql = "select count(*) from pets";
$totalPets = $conn->query($sql)->fetchColumn();

// 今日用藥清單
$sql = "select m.*,p.name as pet_name , u.username
      from medications m
      join pets p on m.pet_id =p.id
      join user u on p.user_id = u.id
      where m.date =CURDATE()
      order by m.date desc";
$stmt = $conn->prepare($sql);
$stmt->execute();
$todayMeds = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>醫護後台首頁</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
window.addEventListener("pageshow",function(event){
    if(event.persisted || performance.getEntriesByType("navigation")[0].type === "back_forward"){
        window.location="index.php";
    }
});
    </script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-end mb-3">
            <span class="me-3">🩺 醫護人員：<?= $username ?></span>
            <form action="api.php?do=logout" method="post" class="d-inline">
                <button class="btn btn-outline-danger btn-sm">登出</button>
            </form>
        </div>

        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">🩺 醫護人員後台總覽</h4>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <a href='admin_health_all.php' class="btn btn-outline-primary w-100 py-3">
                            📋 查看全部健康紀錄
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="admin_health_add.php" class="btn btn-outline-success w-100 py-3">
                            ➕ 新增健康紀錄
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="admin_health_chart_select.php" class="btn btn-outline-info w-100 py-3">
                            📈 查看毛孩體重圖表
                        </a>
                    </div>

                    <!-- <div class="col-md-4">
                        <a href="pets.php" class="btn btn-outline-secondary w-100 py-3">
                            返回毛孩系統
                        </a>
                    </div>-->

                    <div class="col-md-4">
                        <a href="admin_medication_all.php" class="btn btn-outline-info w-100 py-3">
                            💊 用藥紀錄管理
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="" class="btn btn-outline-primary w-100 py-3">
                            🩺看診預約(未完成)
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="admin_user_manage.php" class="btn btn-outline-success w-100 py-3">
                            使用者帳號管理
                        </a>
                    </div>

                </div>
            </div>
        </div>
        <!-- 儀表板統計資訊 -->
        <div class="my-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-success shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title text-muted">🐾 系統內毛孩總數</h5>
                            <h2 class="text-success"><?= $totalPets ?> 隻</h2>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-4">💊 今日用藥提醒</h5>
            <?php if (count($todayMeds) === 0): ?>
                <div class="alert alert-info">今天沒有需要吃藥的紀錄</div>
            <?php else: ?>
                <table class="table table-bordered table-hover bg-white shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th>毛孩</th>
                            <th>飼主</th>
                            <th>藥品名稱</th>
                            <th>劑量</th>
                            <th>頻率</th>
                            <th>備註</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todayMeds as $med): ?>
                            <tr>
                                <td><?= htmlspecialchars($med['pet_name']) ?></td>
                                <td><?= htmlspecialchars($med['username']) ?></td>
                                <td><?= htmlspecialchars($med['medication_name']) ?></td>
                                <td><?= htmlspecialchars($med['dosage']) ?></td>
                                <td><?= htmlspecialchars($med['frequency']) ?></td>
                                <td><?= nl2br(htmlspecialchars($med['notes'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>