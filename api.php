<?php
session_start();
include 'pdo.php';
$do = $_GET['do'] ?? '';
switch ($do) {
    case 'register': //註冊
        $name = $_POST['username'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); //加密存

        // 先檢查帳號是否已存在
        $checksql = "SELECT COUNT(*) FROM `user` WHERE `username` = ?";
        $stmt = $conn->prepare($checksql);
        $stmt->execute([$name]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo "<script>alert('此帳號已存在，請換一個帳號註冊');location.href='register.php'</script>";
        } else {
            // 帳號沒重複才新增
            $sql = "INSERT INTO `user` (  `username`, `password`, `role`, `created_at`) VALUES (?, ?,'user', Now())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $pass]);
            echo "<script>alert('註冊成功，請登入');location.href='login.php'</script>";
        }
        break;

    case 'login':
        $name = $_POST['username'];
        $pass = $_POST['password'];
        $sql = "select * from user where username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"]; // ✅ 這行不能少！

            //依照角色跳轉
            if ($user["role"] == "admin") {
                header("Location: admin_dashboard.php");
            } elseif ($user["role"] == "user") {
                header("Location:pets.php");
            }
            exit;
        } else {
            echo "<script>alert('帳號或密碼錯誤');location.href='login.php'</script>";
        }
        break;

    case 'logout':
        session_start();
        session_unset();
        session_destroy();           // ❌ 清掉所有登入資訊
        header("Location: index.php"); // ⏩ 轉回登入畫面
        exit;

    case 'add_pet': //新增寵物
        $uid = $_SESSION["user_id"];
        $name = $_POST['name'];;
        $type = $_POST['type'];
        $gender = $_POST["gender"];
        $birth = $_POST["birth_date"];

        //  上傳圖片
        $filePath = "";
        if (!empty($_FILES["photo"]["tmp_name"])) {
            $filename = "uploads/" . time() . "_" . $_FILES["photo"]["name"];
            move_uploaded_file($_FILES["photo"]["tmp_name"], $filename);
            $filePath = $filename;
        }

        $sql = "INSERT INTO `pets` (`user_id`, `name`, `type`, `gender`, `birth_date`, `photo_path`, `created_at`) VALUES (?, ?, ?,?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uid, $name, $type, $gender, $birth, $filePath]);
        echo "<script>alert('新增成功');location.href='pets.php'</script>";
        break;

    case "update_pet": //編輯寵物

        if (!isset($_SESSION["user_id"])) {
            echo "<script>alert('請先登入');location.href='login.php'</script>";
            exit;
        }

        $uid = $_SESSION["user_id"];
        $id = $_POST['id'];
        $name = $_POST['name'];
        $gender = $_POST['gender'];
        $type = $_POST['type'];
        $birth = $_POST['birth_date'];

        //是否有上傳新圖片
        $newPhotoPath = '';
        if (!empty($_FILES["photo"]["tmp_name"])) {
            $filename = "uploads/" . time() . "_" . $_FILES["photo"]["name"];
            move_uploaded_file($_FILES["photo"]["tmp_name"], $filename);
            $newPhotoPath = $filename;
        }

        if ($newPhotoPath) {
            // 更新包含圖片
            $sql = "UPDATE pets SET name=?, gender=?, type=?,birth_date=?, photo_path=? WHERE id=? AND user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $gender, $type, $birth, $newPhotoPath, $id, $uid]);
        } else {
            // ✅ 沒有圖片 → 更新其他欄位
            $sql = "UPDATE pets SET name=?, gender=?, type=?, birth_date=? WHERE id=? AND user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $gender, $type, $birth, $id, $uid]);
        }
        echo "<script>alert('編輯成功');location.href='pets.php'</script>";
        break;

    case "delete_pet": //刪除寵物
        if (!isset($_SESSION["user_id"])) {
            echo "<script>alert('請先登入');location.href='login.php'</script>";
            exit;
        }

        $uid = $_SESSION["user_id"];
        $id = $_POST["id"] ?? null;

        if (!$id) {
            echo "<script>alert('沒有指定要刪除的寵物');location.href='pets.php'</script>";
            exit;
        }

        $sql = "delete from pets where id = ? and user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id, $uid]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('刪除成功');location.href='pets.php'</script>";
        } else {
            echo "<script>alert('刪除失敗或無權限');location.href='pets.php'</script>";
        }
        break;

    case "add_diary"; //新增寵物日誌

        if (!isset($_SESSION["user_id"])) {
            echo "<script>alert('請先登入');location.href='login.php'</script>";
            exit;
        }

        $pet_id = $_POST["pet_id"];
        $title = $_POST["title"];
        $content = $_POST["content"];

        $imagePath = "";
        if (!empty($_FILES['image']["tmp_name"])) {
            $filename = "uploads/" . time() . "_" . $_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], $filename);
            $imagePath = $filename;
        }

        $sql = "INSERT INTO `pet_diary` (`pet_id`, `title`, `content`, `image_path`, `created_at`) VALUES (?, ?, ? ,?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$pet_id, $title, $content, $imagePath]);
        echo "<script>alert('日誌新增成功');location.href='diary_list.php?pet_id=$pet_id'</script>";
        break;

    case "update_diary": //修改寵物日誌
        $id = $_POST["id"];
        $title = $_POST["title"];
        $content = $_POST["content"];

        // 確認權限
        $sql = "select d.*,p.user_id,d.pet_id
                from pet_diary d
                join pets p on d.pet_id =p.id
                where d.id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row || $row['user_id'] != $_SESSION["user_id"]) {
            echo "<script>alert('無權限修改');locaiton.href='pets.php'</script>";
            exit;
        }

        $imagePath = $row['image_path'];
        if (!empty($_FILES["image"]["tmp_name"])) {
            $filename = "uploads/" . time() . "_" . $_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], $filename);
            $imagePath = $filename;
        }

        $sql = "update pet_diary set title=?,content=?,image_path=? where id =?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$title, $content, $imagePath, $id]);

        echo "<script>alert('修改完成');location.href='diary_list.php?pet_id={$row['pet_id']}'</script>";
        break;

    case "delete_diary": //刪除日誌
        $id = $_POST['id'];
        $sql = "delete d from pet_diary d
                          join pets p on d.pet_id = p.id
                          where d.id = ? and p.user_id =? ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$id, $_SESSION["user_id"]]);
        echo "<script>alert('已刪除');location.href='pets.php';</script>";
        break;

    case "add_health":

        if (!isset($_SESSION["user_id"])) {
            echo "<script>alert('請先登入');location.href='login.php';</script>";
            exit;
        }

        $pet_id = $_POST["pet_id"];
        $date = $_POST["date"];
        $type = $_POST["type"];
        $value = $_POST["value"];
        $notes = $_POST["notes"];
        // 🛡️ 飼主只能新增體重
        if ($_SESSION["role"] === "user" && $type !== '體重') {
            echo "<script>alert('只有醫護人員能新增非體重紀錄');location.href='pets.php';</script>";
            exit;
        }


        $sql = "INSERT INTO `health_logs` (`pet_id`, `date`, `item_type`,`notes`, `value`,  `created_at`) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$pet_id, $date, $type, $notes, $value]);


        echo "<script>alert('紀錄已新增');location.href='health_list.php?pet_id=$pet_id'</script>";
        break;

    case "update_health":

        $id = $_POST['id'] ?? null;
        $date = $_POST['date'] ?? null;
        $type = $_POST["type"] ?? null;
        $value = $_POST['value'] ?? null;
        $notes = $_POST["notes"] ?? "";

        // ✅ 基本欄位檢查
        if (!is_numeric($id)) {
            echo "<script>alert('無效的紀錄 ID');location.href='pets.php';</script>";
            exit;
        }

        // 🔍 查詢該筆資料 + 擁有者
        $sql =  "SELECT h.pet_id, h.item_type AS old_type, p.user_id
                     FROM health_logs h
                     JOIN pets p ON h.pet_id = p.id
                     WHERE h.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row || $row['user_id'] != $_SESSION['user_id']) {
            echo "<script>alert('無權限修改這筆健康紀錄');location.href='pets.php';</script>";
            exit;
        }

        // 🔐 若是一般使用者，只允許修改體重紀錄
        if ($_SESSION["role"] === "user") {
            if ($row['old_type'] !== '體重' || $type !== '體重') {
                echo "<script>alert('你只能修改體重紀錄');location.href='pets.php';</script>";
                exit;
            }
        }

        // ✅ 執行更新
        $sql = "UPDATE health_logs SET date = ?, item_type = ?, value = ?, notes = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$date, $type, $value, $notes, $id]);

        echo "<script>alert('更新成功');location.href='health_list.php?pet_id={$row['pet_id']}';</script>";
        break;

    case "admin_add_health":

        if (!isset($_SESSION["user_id"])) {
            echo "<script>alert('請先登入');location.href='login.php';</script>";
            exit;
        }

        $pet_id = $_POST["pet_id"];
        $date = $_POST["date"];
        $type = $_POST["type"];
        $value = $_POST["value"];
        $notes = $_POST["notes"];
        // 🛡️ 飼主只能新增體重
        if ($_SESSION["role"] === "user" && $type !== '體重') {
            echo "<script>alert('只有醫護人員能新增非體重紀錄');location.href='pets.php';</script>";
            exit;
        }

        $sql = "INSERT INTO `health_logs` (`pet_id`, `date`, `item_type`,`notes`, `value`,  `created_at`) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$pet_id, $date, $type, $notes, $value]);


        echo "<script>alert('紀錄已新增');location.href='admin_health_all.php'</script>";
        break;

    case 'admin_update_health':
        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
            echo "<script>alert('只有醫護人員可以執行此操作');location.href='login.php';</script>";
            exit;
        }
        $id = $_POST["id"];
        $date = $_POST["date"];
        $type = $_POST["type"];
        $value = $_POST["value"];
        $notes = $_POST["notes"];

        if (!$id || !$date || !$type) {
            echo "<script>alert('請完整填寫表單');location.href='admin_health_edit.php?id=$id';</script>";
            exit;
        }

        $sql = "UPDATE health_logs SET date = ?, item_type = ?, value = ?, notes = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$date, $type, $value, $notes, $id]);

        if ($stmt->rowCount() === 0) {
            echo "<script>alert('已儲存（資料可能與原本相同）');location.href='admin_health_all.php';</script>";
        } else {
            echo "<script>alert('更新成功');location.href='admin_health_all.php';</script>";
        }                //echo "<script>alert('更新成功');location.href='admin_health_all.php'</script>";
        break;

    case "admin_delete_health":
        if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
            echo "<script>alert('只有醫護人員可以執行刪除操作');location.href='login.php';</script>";
            exit;
        }

        $id = $_POST["id"] ?? null;
        if (!$id || !is_numeric($id)) {
            echo "<script>alert('無效的紀錄 ID');location.href='admin_health_all.php';</script>";
            exit;
        }

        $sql = "delete from health_logs where id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('已成功刪除紀錄');location.href='admin_health_all.php';</script>";
        } else {
            echo "<script>alert('刪除失敗，紀錄可能不存在');location.href='admin_health_all.php';</script>";
        }
        break;

    case 'add_food_log':
        if (!isset($_POST["pet_id"], $_POST['date'], $_POST['food_name'])) {
            echo "<script>alert('請填寫必填欄位');history.back();</script>";
            exit;
        }

        $pet_id = $_POST['pet_id'];
        $date = $_POST['date'];
        $meal_time = $_POST['meal_time'] ?? '';
        $food_name = $_POST['food_name'];
        $qty = $_POST['qty'] ?? '';
        $notes = $_POST["notes"] ?? '';

        $sql = "INSERT INTO `food_logs` (`pet_id`, `date`, `meal_time`, `food_name`, `qty`, `notes`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$pet_id, $date, $meal_time, $food_name, $qty, $notes]);

        echo "<script>alert('✅ 飲食紀錄已新增成功！');location.href='food_logs_list.php';</script>";
        break;

    case 'update_food_log';
        $id = $_POST['id'];
        $pet_id = $_POST['pet_id'];
        $date = $_POST['date'];
        $meal_time = $_POST['meal_time'] ?? '';
        $food_name = $_POST['food_name'];
        $qty = $_POST['qty'] ?? '';
        $notes = $_POST['notes'] ?? '';


        $sql = "update food_logs
                              set pet_id=?,date=?,meal_time=?,food_name=?,qty=?,notes=?
                              where id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$pet_id, $date, $meal_time, $food_name, $qty, $notes, $id]);

        echo "<script>alert('更新成功');location.href='food_logs_list.php';</script>";
        break;

    case 'delete_food_log':
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "<script>alert('❌ 缺少 ID！');history.back();</script>";
            exit;
        }

        $sql = "delete from food_logs where id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        echo "<script>alert('✅ 已刪除紀錄！');location.href='food_logs_list.php'</script>";
        break;

    case 'delete_medication':
        if ($_SESSION["role"] !== 'admin') {
            echo json_encode(['success' => false, 'message' => '你沒有權限']);
            exit;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['succuss' => false, 'message' => '缺少紀錄 ID']);
            exit;
        }

        $sql = "delete from medications where id= ?";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([$id]);

        if ($success) {
            echo "<script>alert('已刪除');location.href='admin_medication_all.php';</script>";
        } else {
            echo "<script>alert('刪除失敗');location.href='admin_medication_all.php';</script>";
        }
        exit;
        break;
        case 'add_admin':
            if($_SESSION["role"] !== 'admin'){
                echo "<script>alert('你沒有權限');location.href='admin_user_manage.php';</script>";
                exit;
            }

            $username=trim($_POST["username"]??'');
            $password=$_POST["password"]?? "";

            if(!$username || !$password){
                echo "<script>alert('請填寫帳號與密碼');location.href='admin_user_manage.php';</script>";
                exit;
            }

            $check=$conn->prepare("select id from user where username = ?");
            $check->execute([$username]);
            if($check->fetch()){
                echo "<script>alert('帳號已存在');location.href='admin_user_mange.php;'</script>";
                exit;
            }

            $hashed =password_hash($password,PASSWORD_DEFAULT);
            $stmt=$conn->prepare("INSERT INTO user (username, password, role, created_at) VALUES (?, ?, 'admin', NOW())");
            $stmt->execute([$username,$hashed]);
            echo "<script>alert('新增成功！');location.href='admin_user_manage.php';</script>";
            exit;
        
        break;
}
