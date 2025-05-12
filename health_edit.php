<?php 
session_start();
include "pdo.php";
include 'user_header.php';
/*if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('僅限醫護人員操作');location.href='pets.php';</script>";
    exit;
}*/

if(!isset($_SESSION["user_id"])){
    echo"<script>alert('請先登入');location.href='login.php;'</script>";
    exit;
}

$id=$_GET["id"]?? null;

$sql="SELECT h.*, p.name AS pet_name
        FROM health_logs h
        JOIN pets p ON h.pet_id = p.id
        WHERE h.id = ? AND p.user_id = ? AND h.item_type = '體重'";
        
$stmt=$conn->prepare($sql);
$stmt->execute([$id,$_SESSION['user_id']]);
$log=$stmt->fetch();

if(!$log){
    echo "資料不存在或無權限";
    exit;
}
?>

<h2>編輯<?=$log["pet_name"]?>的健康紀錄</h2>
<form action="api.php?do=update_health" method="post">
    <input type="hidden" name="id" value="<?=$log['id']?>">
    日期:<input type="date" name="date" value="<?=$log['date']?>" required  max="<?= date('Y-m-d') ?>"><br>
    體重 (kg): <input type="text" name="value" value="<?=$log['value']?>" required><br>

    <!--數值:<input type="text" name="value" value="<?=$log['value'] ?>"><br>-->
    備註:<textarea name="notes" rows="4" cols="40"><?=$log['notes']?></textarea><br>
    <button type="submit">儲存修改</button>
</form>