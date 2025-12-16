<?php
require '../_base.php';
admin_require_login();

$_title   = 'Dashboard';
$current  = 'dashboard';

/* ===== 統計數字 ===== */
$customer = $_db->query("SELECT COUNT(*) FROM customer")->fetchColumn();
$product  = $_db->query("SELECT COUNT(*) FROM product")->fetchColumn();
$order    = $_db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

$low_stock = $_db->query("
    SELECT COUNT(*) 
    FROM product 
    WHERE status != 'draft' 
      AND stock <= 10
")->fetchColumn();

/* ===== Order Status ===== */
$rows = $_db->query("SELECT status, COUNT(*) total FROM orders GROUP BY status")->fetchAll();

$st_labels = [];
$st_data   = [];
$st_colors = [];
$color_map = ['pending'=>'#ffc107','completed'=>'#28a745','shipping'=>'#17a2b8','cancelled'=>'#dc3545'];

foreach ($rows as $r) {
    $st_labels[] = ucfirst($r->status);
    $st_data[]   = (int)$r->total;
    $st_colors[] = $color_map[$r->status] ?? '#6c757d';
}

/* ===== Today Total Items Sold ===== */
$today_total = $_db->query("
    SELECT SUM(oi.quantity) 
    FROM order_item oi
    LEFT JOIN orders o ON oi.order_id = o.order_id
    WHERE DATE(o.order_date) = CURDATE()
")->fetchColumn() ?: 0;

$today_label = [date('M j, Y')];
$today_data  = [$today_total];

include '../_head.php';
?>
<div class="content">
    <!-- 四個統計卡片 -->
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:20px; margin:30px 0 50px 0;">
        <div style="background:#f0f8ff; padding:25px; border-radius:8px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin:0; font-size:42px; color:#17a2b8; font-weight:bold;"><?= $customer ?></h3>
            <p style="margin:10px 0 0; color:#666; font-size:18px;">Customer</p>
        </div>
        <div style="background:#f0fff0; padding:25px; border-radius:8px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin:0; font-size:42px; color:#28a745; font-weight:bold;"><?= $product ?></h3>
            <p style="margin:10px 0 0; color:#666; font-size:18px;">Product</p>
        </div>
        <div style="background:#fffbe6; padding:25px; border-radius:8px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin:0; font-size:42px; color:#ffc107; font-weight:bold;"><?= $order ?></h3>
            <p style="margin:10px 0 0; color:#666; font-size:18px;">Orders</p>
        </div>
        <div style="background:#ffe6e6; padding:25px; border-radius:8px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin:0; font-size:42px; color:#dc3545; font-weight:bold;"><?= $low_stock ?></h3>
            <p style="margin:10px 0 0; color:#666; font-size:18px;">Low Stock</p>
        </div>
    </div>

    <hr style="margin:50px 0; border:none; border-top:1px solid #eee;">

    <!-- 圖表區：使用 flex + 固定容器高度 -->
    <div style="display:flex; gap:40px; flex-wrap:wrap; justify-content:center;">
        <!-- 左：Order Status -->
        <div style="flex:1; min-width:300px; max-width:500px; background:white; padding:25px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
            <h4 style="text-align:center; margin-bottom:20px; color:#333;">Order Status</h4>
            <div style="position:relative; height:350px; width:100%;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- 右：Today Total Items Sold -->
        <div style="flex:1; min-width:300px; max-width:500px; background:white; padding:25px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
            <h4 style="text-align:center; margin-bottom:20px; color:#333;">Today Total Items Sold</h4>
            <div style="text-align:center; margin-bottom:30px;">
                <span style="font-size:60px; font-weight:bold; color:#007bff;"><?= $today_total ?></span>
                <p style="margin:10px 0 0; color:#666; font-size:20px;"><?= date('l, F j, Y') ?></p>
            </div>
            <div style="position:relative; height:300px; width:100%;">
                <canvas id="todayChart"></canvas>
            </div>
        </div>
    </div>

 </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Order Status - 圓環圖
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($st_labels) ?>,
        datasets: [{
            data: <?= json_encode($st_data) ?>,
            backgroundColor: <?= json_encode($st_colors) ?>,
            borderWidth: 4,
            borderColor: '#fff',
            hoverOffset: 15
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 20, usePointStyle: true }
            }
        }
    }
});

// Today Total Items Sold - 單一柱狀圖
new Chart(document.getElementById('todayChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($today_label) ?>,
        datasets: [{
            data: <?= json_encode($today_data) ?>,
            backgroundColor: '#007bff',
            borderColor: '#0056b3',
            borderWidth: 2,
            borderRadius: 15,
            maxBarThickness: 80
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php include '../_foot.php'; ?>