<?php
session_start();
include "pdo.php";
include 'user_header.php';
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$pet_id = $_GET["pet_id"] ?? null;
if (!$pet_id) {
    echo "請指定要查看的毛孩ID";
    exit;
}


$sql = "select * from pets where id =? and user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$pet_id, $_SESSION["user_id"]]);
$pet = $stmt->fetch();

if (!$pet) {
    echo "找不到這隻毛孩或你沒有權限查看 ";
    exit;
}

// 取得日誌
$sql = "select * from pet_diary where pet_id=? order by created_at desc";
$stmt = $conn->prepare($sql);
$stmt->execute(["$pet_id"]);
$diaries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pet['name'] ?>的日誌</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-info">📘<?= $pet["name"] ?>的日誌</h3>
            <div class="d-felx gap-2">

                <a href="diary_add.php?pet_id=<?= $pet['id'] ?>" class="btn btn-primary">➕ 新增日誌</a>
                <a href="pets.php" class="btn btn-secondary">返回毛孩清單</a>
            </div>
        </div>
        <?php if (empty($diaries)): ?>
            <div class="alert alert-info">目前還沒有任何日誌喔，快幫 <?= $pet['name'] ?> 寫一篇吧</div>
        <?php else: ?>
            <?php foreach ($diaries as $diary): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">📝<?= $diary["title"] ?></h5>
                        <h6 class="card-subtitle text-muted mb-2">發表於<?= $diary['created_at'] ?></h6>
                        <p class="card-text"><?= nl2br($diary["content"])  ?></p>
                        <?php if (!empty($diary["image_path"])): ?>
                            <img src="<?= $diary['image_path'] ?>" class="img-fluid rounded" style="max-height: 300px; width:300px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="mt-3 d-flex gap-2">
                            <a href="diary_edit.php?id=<?= $diary['id'] ?>" class="btn btn-sm btn-warning">✏️ 編輯</a>
                            <form action="api.php?do=delete_diary" method="post" style="display:inline" onsubmit="return confirm('確定要刪除這篇日誌嗎？')">
                                <input type="hidden" name="id" value="<?= $diary['id'] ?>">
                                <button class="btn btn-sm btn-danger">❌刪除</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

</html>