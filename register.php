<!DOCTYPE html>
<?php session_start(); //可加可不加 
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊系統</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        window.addEventListener("pageshow",function(event){
        // 如果是從快取返回（代表按了上一頁）
        if(event.persisted || performance.getEntriesByType("navigaton")[0].type ==="back_forward"){
            window.location="index.php";
        }
        });
    </script>
</head>

<body class="bg-light">
    <div class="container  mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">🐾 註冊新帳號</h4>
                    </div>
                    <div class="card-body">
                        <form action="api.php?do=register" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">帳號:</label>
                                <input type="text"  class="form-control"name="username" id="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">密碼:</label>
                                <input type="password"  class="form-control"name="password" id="password" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">註冊新帳號</button>
                                <a href="index.php" class="btn btn-outline-secondary">返回首頁</a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-muted text-center">
                        預設身份為 🐾 飼主，如需醫護請由管理者設定
                    </div>
                </div>
            </div>
        </div>
    </div>







</body>

</html>