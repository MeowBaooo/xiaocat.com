<?php
session_start();

// 登入角色驗證：必須是一般使用者（user）
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit;
}

// 防止快取，避免登出後按返回鍵看到快取畫面
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>
