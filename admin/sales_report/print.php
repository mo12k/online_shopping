<?php
require '../_base.php';
admin_require_login();
$_title = 'Sales Report - Print Version';

/* ===== Date filter ===== */
$date_from = req('date_from', date('Y-m-01'));
$date_to   = req('date_to', date('Y-m-d'));

$where = "o.order_date BETWEEN ? AND ?";
$params = [
    $date_from . ' 00:00:00',
    $date_to . ' 23:59:59'
];

/* ===== Top 5 Products ===== */
$sql_product = "
SELECT p.id, p.title, SUM(oi.quantity) total
FROM order_item oi
LEFT JOIN orders o ON oi.order_id = o.order_id
LEFT JOIN product p ON oi.product_id = p.id
WHERE $where
GROUP BY oi.product_id
ORDER BY total DESC
LIMIT 5
";
$stm = $_db->prepare($sql_product);
$stm->execute($params);
$top_products = $stm->fetchAll();

/* ===== Top 5 Categories ===== */
$sql_category = "
SELECT c.category_id, c.category_name, SUM(oi.quantity) total
FROM order_item oi
LEFT JOIN orders o ON oi.order_id = o.order_id
LEFT JOIN product p ON oi.product_id = p.id
LEFT JOIN category c ON p.category_id = c.category_id
WHERE $where
GROUP BY c.category_id
ORDER BY total DESC
LIMIT 5
";
$stm = $_db->prepare($sql_category);
$stm->execute($params);
$top_categories = $stm->fetchAll();

/* ===== Chart Data ===== */
$sql_chart = "
SELECT DATE(o.order_date) d, SUM(oi.quantity) total
FROM order_item oi
LEFT JOIN orders o ON oi.order_id = o.order_id
WHERE $where
GROUP BY DATE(o.order_date)
ORDER BY d
";
$stm = $_db->prepare($sql_chart);
$stm->execute($params);

$labels = [];
$data = [];
while ($r = $stm->fetch(PDO::FETCH_OBJ)) {
    if ($r->d && $r->total > 0) {
        $labels[] = $r->d;
        $data[] = (int)$r->total;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Report - <?= $date_from ?> to <?= $date_to ?></title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 40px;
        color: #333;
        background: #fff;
    }
    h2, h3 { color: #2c3e50; }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 15px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    th {
        background: #f0f0f0;
        font-weight: bold;
    }
    .num { text-align: right; font-weight: bold; color: #e67e22; }
    .chart-container {
        width: 100%;
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background: #f9f9f9;
        border: 1px solid #eee;
        border-radius: 8px;
    }
    .header {
        text-align: center;
        margin-bottom: 30px;
    }
    .date-range {
        font-size: 18px;
        color: #7f8c8d;
        margin: 10px 0;
    }
    .no-print {
        text-align: right;
        margin-bottom: 30px;
    }
    .no-print button {
        padding: 12px 24px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
    }
    @media print {
        .no-print { display: none; }
        body { margin: 10px; }
        .chart-container { page-break-inside: avoid; }
    }
</style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()"> Print Report</button>
</div>

<div class="header">
    <h2>Sales Report</h2>
    <div class="date-range">
        <?= date('F j, Y', strtotime($date_from)) ?> – <?= date('F j, Y', strtotime($date_to)) ?>
    </div>
</div>

<!-- Top 5 Products -->
<h3>Top 5 Products</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Product Name</th>
        <th class="num">Sold</th>
    </tr>
    <?php foreach ($top_products as $p): ?>
    <tr>
        <td><?= $p->id ?></td>
        <td><?= $p->title ?></td>
        <td class="num"><?= $p->total ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($top_products)): ?>
    <tr><td colspan="3" style="text-align:center; color:#999;">No sales in this period</td></tr>
    <?php endif; ?>
</table>

<!-- Top 5 Categories -->
<h3>Top 5 Categories</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Category Name</th>
        <th class="num">Sold</th>
    </tr>
    <?php foreach ($top_categories as $c): ?>
    <tr>
        <td><?= $c->category_id ?></td>
        <td><?= $c->category_name ?></td>
        <td class="num"><?= $c->total ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($top_categories)): ?>
    <tr><td colspan="3" style="text-align:center; color:#999;">No sales in this period</td></tr>
    <?php endif; ?>
</table>

<!-- Chart -->
<div class="chart-container">
    <canvas id="itemChart" height="120"></canvas>
</div>

<script>
new Chart(document.getElementById('itemChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Items Sold Per Day',
            data: <?= json_encode($data) ?>,
            borderColor: '#e67e22',
            backgroundColor: 'rgba(230, 126, 34, 0.1)',
            tension: 0.3,
            fill: true,
            pointRadius: 5,
            pointBackgroundColor: '#e67e22'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

</body>
</html>