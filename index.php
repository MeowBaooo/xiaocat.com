<?php
date_default_timezone_set("Asia/Taipei");
session_start();
$role = $_SESSION["role"] ?? "guest";

$hour = (int)date("H");  // 強制轉數字比較才準確！
echo "<!-- 現在時間 hour: $hour -->";
if ($hour >= 5 && $hour < 12) {
    $greeting = "早安";
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = "午安";
} else {
    $greeting = "晚安";
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>毛孩生活記錄本</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=1600&q=80') no-repeat center center fixed;
      background-size: cover;
      font-family: "Segoe UI", sans-serif;
    }
    .overlay {
      background: rgba(255, 255, 255, 0.9);
      min-height: 100vh;
    }
    .hero {
      padding: 6rem 2rem;
      text-align: center;
    }
    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
      color: #198754;
    }
    .hero p {
      font-size: 1.25rem;
      color: #555;
    }
    .btn-group-custom .btn {
      min-width: 140px;
      font-size: 1rem;
    }
    .navbar-brand {
      font-weight: bold;
    }
  </style>
      <script>
        window.addEventListener("pageshow",function(event){
        // 如果是從快取返回（代表按了上一頁）
        if(event.persisted || performance.getEntriesByType("navigation")[0].type ==="back_forward"){
            window.location="index.php";
        }
        });
    </script>
</head>
<body>
<div class="overlay">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-3">
  <a class="navbar-brand text-success" href="#">🐾 毛孩生活記錄本</a>
  <div class="ms-auto">
    <?php if (isset($_SESSION['user_id'])): ?>
      <span class="me-3">👋 哈囉，<?= htmlspecialchars($_SESSION['username']) ?></span>
      <form action="api.php?do=logout" method="post" class="d-inline">
        <button type="submit" class="btn btn-sm btn-outline-danger">登出</button>
      </form>
    <?php endif; ?>
  </div>
</nav>

<div class="hero">
<h1 class="mb-2 text-success"><?= $greeting ?>，歡迎來到阿包動物醫院！</h1>
  <p class="mb-4 lead">我們致力於照顧每一位毛孩的健康與幸福 🐶🐱</p>
  <p class="mb-1">📍 地址：台北市中山區毛毛路987號</p>
  <p class="mb-1">📞 電話：02-1234-5678</p>
  <p class="mb-4">🕐 營業時間：週一至週六 10:00 - 19:00</p>

  <div class="btn-group-custom d-flex justify-content-center gap-4">
    <?php if (isset($_SESSION['user_id'])): ?>
      <?php if ($role === 'admin'): ?>
        <a href="admin_dashboard.php" class="btn btn-success btn-lg">進入後台</a>
      <?php else: ?>
        <a href="pets.php" class="btn btn-primary btn-lg">我的毛孩</a>
      <?php endif; ?>
    <?php else: ?>
      <a href="login.php" class="btn btn-primary btn-lg">登入</a>
      <a href="register.php" class="btn btn-outline-secondary btn-lg">註冊</a>
    <?php endif; ?>
  </div>
</div>

<!-- 系統功能介紹 -->
 <div class="container mb-5">
  <h4 class="text-center section-title mb-4">📋 系統功能介紹</h4>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title">🩺 健康紀錄</h5>
          <p class="card-text">追蹤疫苗接種、體重變化與就診記錄，協助照護毛孩健康。</p>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
        <h5 class="card-title">🍽️ 飲食紀錄</h5>
        <p class="card-text">記錄每日飲食與份量，幫助建立均衡營養的生活習慣。</p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
        <h5 class="card-title">📘 日誌紀錄</h5>
        <p class="card-text">每天一點點紀錄，收藏毛孩的可愛日常與成長瞬間。</p>
        </div>
      </div>
    </div>
  </div>
 </div>

</div>
</body>
</html>
