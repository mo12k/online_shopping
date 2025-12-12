<?php
// order-confirmation.php
require '../../_base.php';
$current = 'order';
$_title = 'Order Confirmation';

$order_id = get('id');
if (!$order_id) redirect('cart.php');

// 检查登录
if (!isset($_SESSION['customer_id'])) {
    temp('info', 'Please login to view order');
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'];

// 获取订单详情
$order = get_order_by_id($order_id, $customer_id);
if (!$order) {
    temp('error', 'Order not found');
    redirect('cart.php');
}

// 获取订单商品
$order_items = get_order_items($order_id);

include '../_head.php';
?>

<div class="order-confirmation-container">
    <div class="confirmation-header">
        <div class="success-icon">✓</div>
        <h1>Order Confirmed!</h1>
        <p class="order-number">Order #: <?= $order_id ?></p>
    </div>
    
    <div class="confirmation-content">
        <div class="order-details">
            <h2>Order Details</h2>
            
            <div class="detail-row">
                <span class="label">Order Status:</span>
                <span class="value status-<?= $order->status ?>">
                    <?= ucfirst($order->status) ?>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="label">Order Date:</span>
                <span class="value"><?= date('F j, Y, g:i a', strtotime($order->order_date)) ?></span>
            </div>
            
            <div class="detail-row">
                <span class="label">Total Amount:</span>
                <span class="value price">RM <?= number_format($order->total_amount, 2) ?></span>
            </div>
            
            <div class="note">
                <strong>Note:</strong> This was a simulated payment for demonstration purposes.
            </div>
        </div>
        
        <div class="order-items">
            <h3>Order Items</h3>
            <?php foreach ($order_items as $item): ?>
                <div class="order-item">
                    <div class="item-info">
                        <strong><?= encode($item->title) ?></strong>
                        <div class="item-meta">
                            Quantity: <?= $item->quantity ?> × RM <?= number_format($item->price, 2) ?>
                        </div>
                    </div>
                    <div class="item-subtotal">
                        RM <?= number_format($item->quantity * $item->price, 2) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="actions">
            <a href="../page/product.php" class="btn-continue">
                Continue Shopping
            </a>
            <a href="order-history.php" class="btn-history">
                View Order History
            </a>
        </div>
    </div>
</div>

<style>
.order-confirmation-container {
    max-width: 800px;
    margin: 40px auto;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.confirmation-header {
    background: linear-gradient(135deg, #2e7d32, #4caf50);
    color: white;
    padding: 50px 40px;
    text-align: center;
}

.success-icon {
    font-size: 80px;
    margin-bottom: 20px;
}

.confirmation-header h1 {
    margin: 0 0 10px 0;
    font-size: 36px;
}

.order-number {
    font-size: 18px;
    opacity: 0.9;
}

.confirmation-content {
    padding: 40px;
}

.order-details {
    background: #f9f9f9;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.order-details h2 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 15px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.detail-row:last-child {
    border-bottom: none;
}

.label {
    color: #666;
    font-weight: 500;
}

.value {
    font-weight: 600;
    color: #333;
}

.status-paid {
    color: #2e7d32;
}

.status-pending {
    color: #ff9800;
}

.price {
    color: #6d4c41;
    font-size: 20px;
}

.note {
    margin-top: 20px;
    padding: 15px;
    background: #fff3cd;
    border-radius: 8px;
    color: #856404;
    font-size: 14px;
}

.order-items {
    margin: 30px 0;
}

.order-items h3 {
    margin-bottom: 20px;
    color: #333;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 8px;
    margin-bottom: 10px;
}

.item-meta {
    color: #666;
    font-size: 14px;
    margin-top: 5px;
}

.actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid #f0f0f0;
}

.btn-continue, .btn-history {
    padding: 14px 30px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-continue {
    background: #6d4c41;
    color: white;
}

.btn-history {
    background: #f0f0f0;
    color: #333;
}

.btn-continue:hover, .btn-history:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>

<?php include '../../_foot.php'; ?>