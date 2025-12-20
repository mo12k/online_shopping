<?php
require '../_base.php';
admin_require_login();
$_title = 'Sales Report';
$current = 'report';

$date_from = req('date_from', date('Y-m-01'));
$date_to   = req('date_to', date('Y-m-d'));

$where = "o.order_date BETWEEN ? AND ?";
$params = [
    $date_from . ' 00:00:00',
    $date_to . ' 23:59:59'
];

$sql_product = "
            SELECT 
                p.id,
                p.title,
                SUM(oi.quantity) total
            FROM order_item oi
            LEFT JOIN orders o ON oi.order_id = o.order_id
            LEFT JOIN product p ON oi.product_id = p.id
            WHERE $where 
            AND o.status = 'completed'
            GROUP BY oi.product_id
            ORDER BY total DESC
            LIMIT 5
            ";
$stm = $_db->prepare($sql_product);
$stm->execute($params);
$top_products = $stm->fetchAll();


$sql_category = "
            SELECT 
                c.category_id,
                c.category_name,
                SUM(oi.quantity) total
            FROM order_item oi
            LEFT JOIN orders o ON oi.order_id = o.order_id
            LEFT JOIN product p ON oi.product_id = p.id
            LEFT JOIN category c ON p.category_id = c.category_id
            WHERE $where
            AND o.status = 'completed'
            GROUP BY c.category_id
            ORDER BY total DESC
            LIMIT 5
            ";
$stm = $_db->prepare($sql_category);
$stm->execute($params);
$top_categories = $stm->fetchAll();


$sql_chart = "
            SELECT 
                DATE(o.order_date) d,
                SUM(oi.quantity) total
            FROM order_item oi
            LEFT JOIN orders o ON oi.order_id = o.order_id
            WHERE $where
            AND o.status = 'completed'
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
        $data[]   = (int)$r->total;
    }
}


if (count($labels) !== count($data)) {
   
    $labels = [];
    $data   = [];
}
include '../_head.php';
?>

<div class="content">

   
    <table style="width:100%; border-collapse: collapse; margin-bottom: 40px;">
        <tr>
           
            <td style="width:30%; vertical-align: top; padding: 15px; background:#f9f9f9; border-radius:8px;">
                <h4 style="margin-top:0; color:#333;">Date Filter</h4>
                <form id="filterForm" method="get">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Date From</label>
                    <input type="date" name="date_from" value="<?= $date_from ?>" 
                           style="width:100%; padding:8px; margin-bottom:15px; border:1px solid #ccc; border-radius:4px;">

                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Date To</label>
                    <input type="date" name="date_to" value="<?= $date_to ?>" 
                           style="width:100%; padding:8px; margin-bottom:20px; border:1px solid #ccc; border-radius:4px;">

                    <button type="submit" 
                            style="width:100%; padding:10px; background:#ff9800; color:white; border:none; border-radius:4px; font-size:16px; cursor:pointer;">
                        Apply Filter
                    </button>
                </form>

              <a href="/admin/sales_report/print.php?<?=$date_from ?>&date_to=<?= $date_to ?>" 
                target="_blank"
                style="display:block; width:50%; margin-top:10px; padding:10px; background:#27ae60; color:white; text-align:center; text-decoration:none; border-radius:4px; font-size:16px; cursor:pointer;">
                     Print 
              </a>
            </td>

            
            <td style="width:35%; vertical-align: top; padding: 15px;">
                <h4 style="margin-top:0; color:#333;">Top 5 Products</h4>
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="background:#fff3e0;">
                            <th style="padding:10px; text-align:left;">Id</th>
                            <th style="padding:10px; text-align:left;">Name</th>
                            <th style="padding:10px; text-align:right;">Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_products as $p): ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:10px;"><?= $p->id ?></td>
                            <td style="padding:10px;"><?= $p->title ?></td>
                            <td style="padding:10px; text-align:right; font-weight:bold; color:#ff9800;"><?= $p->total ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($top_products)): ?>
                        <tr>
                            <td colspan="3" style="padding:20px; text-align:center; color:#999; font-style:italic;">
                                No data
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </td>

            
            <td style="width:35%; vertical-align: top; padding: 15px;">
                <h4 style="margin-top:0; color:#333;">Top 5 Categories</h4>
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="background:#fff3e0;">
                            <th style="padding:10px; text-align:left;">Id</th>
                            <th style="padding:10px; text-align:left;">Name</th>
                            <th style="padding:10px; text-align:right;">Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_categories as $c): ?>
                        <tr style="border-bottom:1px solid #eee;">
                             <td style="padding:10px;"><?= $c->category_id ?></td>
                            <td style="padding:10px;"><?= $c->category_name ?></td>
                            <td style="padding:10px; text-align:right; font-weight:bold; color:#ff9800;"><?= $c->total ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($top_categories)): ?>
                        <tr>
                            <td colspan="3" style="padding:20px; text-align:center; color:#999; font-style:italic;">
                                No data
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    
    <div style="background:white; padding:25px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
        <h4 style="margin-top:0; color:#333;">
            Order Item Trend 
            <span style="font-size:14px; color:#666; font-weight:normal;">
                (<?= date('M j, Y', strtotime($date_from)) ?> – <?= date('M j, Y', strtotime($date_to)) ?>)
            </span>
        </h4>
        <canvas id="itemChart" height="100"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>


/* chart */
new Chart(document.getElementById('itemChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Items Sold',
            data: <?= json_encode($data) ?>,
            borderColor: '#ff9800',
            backgroundColor: 'rgba(255,152,0,.1)',
            tension: .3,
            fill: true
        }]
    }
});
</script>