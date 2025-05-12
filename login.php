<!DOCTYPE html>
<?php session_start(); //不加這行會出錯
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入系統</title>
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

<body calss="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow0">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🐾 毛孩生活記錄本登入</h4>
                    </div>
                    <div class="card-body">
                        <form action="api.php?do=login" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">帳號：</label>
                                <input type="text" class="form-control" name="username" id="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">密碼:</label>
                                <input type="password"class="form-control" name="password" id="password" required><br>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit">登入</button>
                                <a href="register.php" class="btn btn-outline-secondary">註冊帳號</a>
                                <a href="index.php" class="btn btn-outline-secondary">返回首頁</a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-muted text-center">
                        一般使用者 / 醫護人員 請用帳密登入，訪客功能待開發
                    </div>
                </div>
            </div>
        </div>



</body>

</html>