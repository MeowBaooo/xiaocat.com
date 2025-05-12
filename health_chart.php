<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>體重圖表</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php
session_start();
include 'pdo.php';
include 'user_header.php';
$pet_id = $_GET["pet_id"] ?? null;

if (!$pet_id || !isset($_SESSION["user_id"])) {
    header("Location:pets.php");
    exit;
}

if ($_SESSION['role'] === 'admin') {
    $sql = "SELECT * FROM pets WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$pet_id]);   
} else {
    $sql = "SELECT * FROM pets WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$pet_id, $_SESSION["user_id"]]);     
}

$pet = $stmt->fetch();
if (!$pet) {
    echo "找不到毛孩或無權限";
    exit;
}

$sql = "SELECT date, value
        FROM health_logs
        WHERE pet_id = ? AND item_type = '體重'
        ORDER BY date";
$stmt = $conn->prepare($sql);
$stmt->execute([$pet_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$weights = [];
foreach ($data as $row) {
    $labels[] = $row["date"];
    $weights[] = floatval($row["value"]);
}
?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-success">📈 <?= htmlspecialchars($pet["name"]) ?> 的體重變化圖</h4>
    <a href="<?= $_SESSION['role'] === 'admin' ? 'admin_health_chart_select.php' : 'health_list.php?pet_id=' . $pet_id ?>" class="btn btn-outline-primary btn-sm">🔙 返回上一頁</a>
  </div>

  <div class="mb-3">
    <label class="form-label">選擇月份</label>
    <select id="monthSelect" class="form-select w-auto">
      <option value="all">全部</option>
      <?php
        $months = array_unique(array_map(fn($d) => substr($d["date"], 0, 7), $data));
        foreach ($months as $m) {
          echo "<option value=\"$m\">$m</option>";
        }
      ?>
    </select>
  </div>

  <div class="card p-3 shadow-sm">
    <canvas id="weightChart"></canvas>
  </div>
</div>

<script>
const fullLabels = <?= json_encode($labels) ?>;
const fullData = <?= json_encode($weights) ?>;

const ctx = document.getElementById('weightChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [...fullLabels],
        datasets: [
          {
            label: '體重 (kg)',
            data: [...fullData],
            borderWidth: 2,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            fill: true,
            tension: 0.3,
            pointRadius: 5,
            pointHoverRadius: 7
          },
          {
            label: '平均值',
            data: [],
            borderColor: 'rgba(255, 99, 132, 0.8)',
            borderDash: [5, 5],
            pointRadius: 0,
            fill: false,
            tension: 0
          }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label === '平均值'
                          ? `平均：${context.parsed.y.toFixed(2)} kg`
                          : `體重：${context.parsed.y} kg`;
                    }
                }
            }
        },
        scales: {
            y: {
                title: {
                    display: true,
                    text: '公斤 (kg)'
                },
                beginAtZero: false
            },
            x: {
                title: {
                    display: true,
                    text: '日期'
                }
            }
        }
    }
});

const monthSelect = document.getElementById('monthSelect');
monthSelect.addEventListener('change', () => {
  const selected = monthSelect.value;
  let filteredLabels = [...fullLabels];
  let filteredData = [...fullData];

  if (selected !== 'all') {
    filteredLabels = [];
    filteredData = [];
    for (let i = 0; i < fullLabels.length; i++) {
      if (fullLabels[i].startsWith(selected)) {
        filteredLabels.push(fullLabels[i]);
        filteredData.push(fullData[i]);
      }
    }
  }

  const avg = filteredData.length > 0
    ? filteredData.reduce((a, b) => a + b, 0) / filteredData.length
    : null;

  chart.data.labels = filteredLabels;
  chart.data.datasets[0].data = filteredData;
  chart.data.datasets[1].data = avg ? Array(filteredLabels.length).fill(avg) : [];
  chart.update();
});
</script>
</body>
</html>
