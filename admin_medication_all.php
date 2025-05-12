<?php
session_start();
include 'pdo.php';
include 'admin_header.php';
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('你沒有權限');location.href='login.php'</script>";
    exit;
}

// 取得毛孩資料
$sql = "SELECT pets.id, pets.name, user.username
        FROM pets 
        JOIN user ON pets.user_id = user.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 新增資料
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pet_id = $_POST['pet_id'];
    $date = $_POST["date"];
    $name = $_POST["medication_name"];
    $dosage = $_POST["dosage"];
    $frequency = $_POST["frequency"];
    $notes = $_POST["notes"];

    $sql = "INSERT INTO medications (pet_id, date, medication_name, dosage, frequency, notes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$pet_id, $date, $name, $dosage, $frequency, $notes]);

    echo "<script>alert('新增成功！');location.href='admin_medication_all.php';</script>";
    exit;
}

// 抓所有用藥紀錄
$sql = "SELECT m.*, p.name AS pet_name, u.username
        FROM medications m
        JOIN pets p ON m.pet_id = p.id
        JOIN user u ON p.user_id = u.id
        ORDER BY m.date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>用藥紀錄管理</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery + slideToggle 動畫 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(function() {
            $('#toggleAdd').click(function() {
                $('#addForm').slideToggle(); // 展開/收合表單
            });
        });
    </script>

    <style>
        .pet-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: .375rem .375rem 0 0;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h3 class="text-primary mb-4">📋 全部毛孩用藥紀錄</h3>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">返回後台首頁</a>
        <!-- 按鈕：用 jQuery 控制 -->
        <h4 class="text-success" id="toggleAdd" style="cursor:pointer;">➕ 新增用藥紀錄</h4>

        <!-- 收合區域 -->
        <div id="addForm" style="display: none;" class="card card-body shadow-sm mb-4">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">選擇寵物</label>
                    <select name="pet_id" class="form-select" required>
                        <option value="">請選擇</option>
                        <?php foreach ($pets as $pet): ?>
                            <option value="<?= $pet['id'] ?>">
                                <?= htmlspecialchars($pet['name']) ?>（飼主：<?= htmlspecialchars($pet['username']) ?>）
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">用藥日期</label>
                    <input type="date" name="date" class="form-control" required  max="<?= date('Y-m-d') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">藥品名稱</label>
                    <input type="text" name="medication_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">劑量</label>
                    <input type="text" name="dosage" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">頻率</label>
                    <input type="text" name="frequency" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">備註</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-success">💾 儲存紀錄</button>
            </form>
        </div>

        <!-- 用藥紀錄列表 -->
        <table class="table table-bordered table-striped bg-white shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th>用藥日期</th>
                    <th>毛孩名稱</th>
                    <th>飼主</th>
                    <th>藥品名稱</th>
                    <th>劑量</th>
                    <th>頻率</th>
                    <th>備註</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['date'] ?></td>
                        <td><?= $log['pet_name'] ?></td>
                        <td><?= $log['username'] ?></td>
                        <td><?= $log['medication_name'] ?></td>
                        <td><?= $log['dosage'] ?></td>
                        <td><?= $log['frequency'] ?></td>
                        <td><?= nl2br($log['notes']) ?></td>
                        <td class="d-flex gap-1">
                            <a href="admin_medication_edit.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                            <form method="post" action="api.php?do=delete_medication" onsubmit="return confirm('確定要刪除？')">
                                <input type="hidden" name="id" value="<?= $log['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">❌</button>
                            </form>
                        </td>
                            </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>