<?php
session_start();
include 'pdo.php';

//沒登入就踢回登入頁面
if (!isset($_SESSION['user_id'])) {
    header("Location:login.php");
    exit;
}

/*抓自己的寵物
$sql = "select * from pets where user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);*/

// 抓自己的寵物 + 是否有用藥紀錄
$sql = "SELECT pets.*,
        EXISTS (
            SELECT 1 FROM medications WHERE medications.pet_id = pets.id
        ) AS has_medication
        FROM pets
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$role = $_SESSION['role'] ?? 'guest';
$username = $_SESSION["username"] ?? '訪客';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的毛孩</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pet-img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: .375rem .375rem 0 0;
        }
    </style>

    <!--放 jQuery + 簡單動畫-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(function() {
            $('#toggleAdd').click(function() {
                $('#addForm').slideToggle(); // 切換顯示
            });
        });
    </script>
    <script>
        window.addEventListener("pageshow",function(event){
        // 如果是從快取返回（代表按了上一頁）
        if(event.persisted || performance.getEntriesByType("navigation")[0].type ==="back_forward"){
            window.location="index.php";
        }
        });
    </script>
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">🐾 我的毛孩</h2>
            <!--使用者資訊  -->
            <div>
                <span class="me-3"> 使用者:<?= $username ?>(<?= $role === 'admin' ? '🩺 醫護人員' : '🐾 飼主' ?>)</span>
                <form action="api.php?do=logout" method="post" class="d-inline">
                    <button class="btn btn-outline-danger btn-sm">登出</button>
                </form>
            </div>
        </div>
        <hr>
        <h4 class="text-success" id="toggleAdd" style="cursor:pointer;">➕ 新增毛孩</h4>
        <form id="addForm" action="api.php?do=add_pet" method="post" enctype="multipart/form-data" class="bg-white p-4 roynded shadow-sm" style="display: none;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">名字</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">品種</label>
                    <input type="text" name="type" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">性別</label>
                    <select name="gender" class="form-select">
                        <option value="公">公</option>
                        <option value="母">母</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">生日</label>
                    <input type="date" name="birth_date" class="form-control" required  max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">頭像</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">新增毛孩</button>
                </div>
            </div>
        </form>

        <div class="row">
            <?php foreach ($pets as $pet): ?>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <?php if (!empty($pet['photo_path'])): ?>
                            <img src="<?= $pet['photo_path'] ?>" class="pet-img" alt="<?= $pet['name'] ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= $pet["name"] ?>(<?= $pet['gender'] ?>)</h5>
                            <p class="card-text">
                                品種:<?= $pet["type"] ?><br>
                                生日:<?= $pet["birth_date"] ?> 
                                <?php if ($pet['has_medication']): ?>
                                <span class="badge bg-danger mb-2">💊 有用藥紀錄</span>
                            <?php endif; ?>
                            </p>
                           
                            <div class="d-flex flex-wrap gap-2">
                                <a href="edit_pet.php?id=<?= $pet['id'] ?>" class="btn btn-sm btn-warning">編輯</a>
                                <form action="api.php?do=delete_pet" method="post" onsubmit="return confirm('確定要刪除可愛的<?= $pet['name'] ?>嗎?')">
                                    <input type="hidden" name="id" value="<?= $pet['id'] ?>">
                                    <button class="btn btn-sm btn-danger">❌ 刪除</button>
                                </form>
                                <a href="diary_list.php?pet_id=<?= $pet['id'] ?>" class="btn btn-sm btn-info">📘 日誌</a>
                                <a href="health_list.php?pet_id=<?= $pet['id'] ?>" class="btn btn-sm btn-success">🩺 健康</a>
                                <a href="medication_list.php?pet_id=<?= $pet['id'] ?>" class="btn btn-sm btn-outline-danger">💊 用藥</a>
                                <a href="food_logs_list.php?pet_id=<?= $pet['id'] ?>" class="btn btn-sm btn-outline-dark">📄 全部飲食紀錄</a>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>




        <?php
        if (isset($_GET["pet_id"])) {
            $pet_id = $_GET["pet_id"];

            // 取得該寵物的飲食紀錄
            $sql = "SELECT * FROM food_logs WHERE pet_id = ? ORDER BY date DESC, id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$pet_id]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

            <div class="mt-5">
                <h3 class="text-primary">📋 飲食紀錄</h3>
                <?php if (count($logs) > 0): ?>
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>日期</th>
                                <th>時間</th>
                                <th>食物</th>
                                <th>份量</th>
                                <th>備註</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= $log['date'] ?></td>
                                    <td><?= $log['meal_time'] ?></td>
                                    <td><?= $log['food_name'] ?></td>
                                    <td><?= $log['qty'] ?></td>
                                    <td><?= nl2br(htmlspecialchars($log['notes'])) ?></td>
                                    <td>
                                        <a href="food_log_edit.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-warning">📝 編輯</a>
                                        <a href="api.php?do=delete_food_log&id=<?= $log['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('確定要刪除嗎？')">🗑️ 刪除</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">目前沒有飲食紀錄</div>
                <?php endif; ?>
            </div>

        <?php } ?>

</body>

</html>