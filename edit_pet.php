<?php
session_start();
include 'pdo.php';
include 'user_header.php';
// 安全檢查
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$user_id = $_SESSION["user_id"];

// 讀取這筆寵物資料（只能讀取自己的）
$sql = "select * from pets where id = ? and user_id=?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id, $user_id]);
$pet = $stmt->fetch();

if (!$pet) {
    echo "資料不存在或無權限";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯毛孩資料</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0">✏️ 編輯毛孩資料-<?= $pet["name"] ?></h4>
            </div>
            <di class="card-body">
                <form action="api.php?do=update_pet" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $pet['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">名字:</label>
                            <input type="text" name="name" class="form-control" value="<?= $pet['name'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">品種:</label>
                            <input type="text" name="type" class="form-control" value="<?= $pet['type'] ?>">
                        </div>
                        <div class="clo-md-6">
                            <label class="form-label">性別:</label>
                            <select name="gender" class="form-select">
                                <option value="公" <?= ($pet['gender'] == '公') ? 'selected' : '' ?>>公</option>
                                <option value="母" <?= ($pet['gender'] == '母') ? 'selected' : '' ?>>母</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"> 生日:</label>
                            <input type="date" class="form-control" name="birth_date" value="<?= $pet['birth_date'] ?>" required  max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">更換照片(可略過):</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <?php if (!empty($pet['photo_path'])): ?>
                                <img src="<?= $pet['photo_path'] ?>" width="100"><br>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-warning">儲存變更</button>
                        <a href="pets.php" class="btn btn-secondary">返回</a>
                    </div>
                </form>
        </div>

    </div>

    </div>

</body>

</html>