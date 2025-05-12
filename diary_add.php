<?php
session_start();
include 'pdo.php';
include 'user_header.php';
$pet_id = $_GET["pet_id"] ?? "";

if (!isset($_SESSION["user_id"])) {
    header("Location:login.php");
    exit;
}

// 抓自己的所有寵物，讓使用者選要幫哪一隻寫日誌
$sql = "select * from pets where user_id=?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION["user_id"]]);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增毛孩日誌</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">➕ 新增毛孩日誌</h4>
            </div>
            <div class="card-body">
                <form action="api.php?do=add_diary" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">選擇毛孩</label>
                        <select name="pet_id" class="form-select" required>
                            <?php foreach ($pets as $pet): ?>
                                <option value="<?= $pet['id'] ?>" <?= $pet['id'] == $pet_id ? "selected" : "" ?>><?= $pet['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">標題</label>
                        <input type="text" class="form-control" name="title" required><br>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">內容</label>
                        <textarea name="content" rows="5" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">上傳照片（可選）</label>
                       <input type="file"  class="form-control" name="image" accept="image/*">
                    </div>
                    <div class="text-end">
          <button type="submit" class="btn btn-primary">儲存日誌</button>
        <a href="pets.php" class="btn btn-secondary">返回毛孩清單</a>          
        </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
