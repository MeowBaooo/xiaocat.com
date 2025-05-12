<?php
session_start();
include "pdo.php";
include 'user_header.php';
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"] ?? null;
if (!$id) {
    echo "未指定日誌ID";
    exit;
}

// 撈出日誌與毛孩資料
$sql = "select d.* ,p.name as pet_name
      from pet_diary d
      join pets p on d.pet_id=p.id
      where d.id = ? and p.user_id =?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id, $_SESSION["user_id"]]);
$diary = $stmt->fetch();

if (!$diary) {
    echo "找不到日誌或無權限";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯<?= $diary['pet_name'] ?>的日誌</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0">✏️ 編輯<?= $diary['pet_name'] ?>的日誌</h4>
            </div>

            <div class="card-body">
                <form action="api.php?do=update_diary" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $diary['id'] ?>">


                    <div class="mb-3">
                        <label class="form-label">標題</label>
                        <input type="text" name="title" class="form-control" value="<?= $diary['title'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">內容</label>
                        <textarea name="content" rows="5" class="form-control" required><?= $diary['content'] ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">更新圖片（可不選）</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if (!empty($diary["image_path"])): ?>
                            <img src="<?= $diary['image_path'] ?>" class="img-thumbnail mt-2 " style="height: 200px; object-fit: cover;"><br>
                        <?php endif; ?>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">儲存變更</button>
                        <a href="diary_list.php?pet_id=<?= $diary['pet_id'] ?>" class="btn btn-secondary">返回</a>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
</body>

</html>