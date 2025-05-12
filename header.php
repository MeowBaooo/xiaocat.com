<?php


if(!isset($_SESSION["user_id"])){
    header("Location:login.php");
    exit;
}

// ✅ 抓角色與使用者名稱（如果有的話）
$role=$_SESSION["role"]?? "guest";
$username=$_SESSION["username"]??"未登入";

// ✅ 角色顯示轉換
$role_label=[
    'admin' =>'醫護人員',
    'user' =>'飼主',
    'guest' => '訪客'
][$role] ?? '未知';
?>
<!-- 📦 使用者資訊列 -->
<div style="position:absolute;top:10px;right:10px;padding:8px;background:#eee;border-radius:8px">
使用者:<?=$_SESSION["username"]?><br>
身分:<?=$role_label ?><br>
<button onclick="location.href='api.php?do=logout'" ;style="color:red;">登出</button>

</div>