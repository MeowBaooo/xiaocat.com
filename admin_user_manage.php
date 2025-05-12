<?php
session_start();
include 'pdo.php';
include 'admin_header.php';
if ($_SESSION["role"] !== 'admin') {
    echo "<script>alert('你不是管理員');location.href='login.php';</script>";
    exit;
}

$sql = "select id, username,role,created_at from user order by created_at desc";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>使用者帳號管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(function() {
            $('#toggleAdd').click(function() {
                $('#addForm').slideToggle(); // 展開/收合新增表單
            });
        });
    </script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h3 class="text-primary mb-3">👥 使用者帳號管理</h3>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">← 返回儀表板</a>

        <h4 class="text-success mb-2" id="toggleAdd" style="cursor:pointer;">➕ 新增醫護人員</h4>
        <form id="addForm" method="post" action="api.php?do=add_admin" class="bg-white p-4 rounded shadow-sm mb-4" style="display:none;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">使用者名稱</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">密碼</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">💾 新增醫護帳號</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered table-striped bg-white shadow-sm">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>使用者名稱</th>
                    <th>權限</th>
                    <th>註冊時間</th>
                    <!--<th>操作</th>-->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['role'] === 'admin' ? '🩺 醫護人員' : '🐾 飼主' ?></td>
                        <td><?= $user['created_at'] ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>