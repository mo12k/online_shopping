<?php
require '../_base.php';

if (!isset($_SESSION['customer_id'])) {
    $_SESSION['temp_info'] = 'Please login to view order';
    redirect('../../page/login.php');
    exit;
}

$customer_id = $_SESSION['customer_id'];

$hash = $_GET['id'] ?? '';
$order_id = decode_id($hash);

if ($order_id <= 0) {
    redirect('order_history.php');
    exit;
}

$sql = 'SELECT o.*, ca.address, ca.city, ca.state, ca.postcode 
        FROM orders o 
        JOIN customer_address ca ON o.address_id = ca.address_id 
        WHERE o.order_id = ? AND o.customer_id = ?';
$stm = $_db->prepare($sql);
$stm->execute([$order_id, $customer_id]);
$order = $stm->fetch();

if (!$order) {
    redirect('order_history.php');
    exit;
}

$sql = 'SELECT oi.*, p.title, p.photo_name, oi.price_each 
        FROM order_item oi 
        JOIN product p ON oi.product_id = p.id 
        WHERE oi.order_id = ? 
        ORDER BY oi.order_item_id';
$stm = $_db->prepare($sql);
$stm->execute([$order_id]);
$order_items = $stm->fetchAll();
?>

    <style>
        
        .order-confirmation-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideIn 0.6s ease-out;
        }
        
        .confirmation-header {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            padding: 60px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .confirmation-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1%, transparent 1%);
            background-size: 50px 50px;
            animation: float 20s linear infinite;
        }
        
        .confirmation-header h1 {
            margin: 0 0 10px 0;
            font-size: 42px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .order-number {
            font-size: 20px;
            opacity: 0.9;
            background: rgba(255,255,255,0.2);
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            margin-top: 10px;
        }
        
        .confirmation-content {
            padding: 50px;
        }
        
        .order-details {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .order-details h2 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 3px solid #2ecc71;
            padding-bottom: 15px;
            margin-bottom: 25px;
            font-size: 28px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
        
        .detail-label {
            display: block;
            color: #7f8c8d;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .detail-value {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 600;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .total-amount {
            text-align: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 3px dashed #dee2e6;
        }
        
        .total-label {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .total-value {
            font-size: 36px;
            color: #5d4037;
            font-weight: 700;
        }
        
        .order-items {
            margin: 40px 0;
        }
        
        .order-items h3 {
            margin-bottom: 25px;
            color: #2c3e50;
            font-size: 24px;
            border-bottom: 2px solid #8d6e63;
            padding-bottom: 10px;
        }
        
        .items-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid #8d6e63;
        }
        
        .order-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .item-image {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .no-image {
            width: 80px;
            height: 100px;
            background: #ecf0f1;
            border-radius: 8px;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #95a5a6;
            font-size: 12px;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .item-meta {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .item-price {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            text-align: right;
            min-width: 150px;
        }
        
        .actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 50px;
            padding-top: 40px;
            border-top: 3px solid #f1f2f6;
        }
        
        .btn {
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
        }
        
        .btn-continue {
            background: linear-gradient(135deg, #8d6e63, #5d4037);
            color: white;
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }
        
        .btn-history {
            background: white;
            color: #5d4037;
            border: 2px solid #8d6e63;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.2);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .btn:active {
            transform: translateY(-1px);
        }
        
        .note {
            margin-top: 30px;
            padding: 20px;
            background: #fff3cd;
            border-radius: 10px;
            color: #856404;
            font-size: 14px;
            border-left: 5px solid #ffc107;
        }
    </style>

    <title><?= $_page_title ?? 'Bookstore' ?></title>
    <link rel="shortcut icon" href="../../images/book.png" type="image/x-icon">

    <div class="order-confirmation-container">
        <div class="confirmation-header">
            <h1>Order Confirmed!</h1>
            <p class="order-number">Order : <?= htmlspecialchars($order_id) ?></p>
        </div>
        
        <div class="confirmation-content">
            <div class="order-details">
                <h2>Order Details</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Order Status</span>
                        <span class="detail-value">
                            <span class="status-badge status-<?= $order->status ?>">
                                <?= ucfirst($order->status) ?>
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Order Date</span>
                        <span class="detail-value"><?= date('F j, Y, g:i a', strtotime($order->order_date)) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Delivery Address</span>
                        <span class="detail-value">
                            <?= htmlspecialchars($order->address) ?><br>
                            <?= htmlspecialchars($order->city) ?>, <?= htmlspecialchars($order->state) ?>
                            <?= htmlspecialchars($order->postcode) ?>
                        </span>
                    </div>
                </div>
                
                <div class="total-amount">
                    <div class="total-label">Total Amount</div>
                    <div class="total-value">RM <?= number_format($order->total_amount, 2) ?></div>
                </div>
            </div>
            
            <div class="order-items">
                <h3>Order Items</h3>
                <div class="items-list">
                    <?php foreach ($order_items as $item): ?>
                        <div class="order-item">
                            <?php if ($item->photo_name): ?>
                                <img src="../upload/<?= htmlspecialchars($item->photo_name) ?>"
                                     alt="<?= htmlspecialchars($item->title) ?>"
                                     class="item-image">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                            
                            <div class="item-info">
                                <div class="item-title"><?= htmlspecialchars($item->title) ?></div>
                                <div class="item-meta">
                                    Quantity: <?= $item->quantity ?>
                                    × RM <?= number_format($item->price_each, 2) ?>
                                </div>
                            </div>
                            
                            <div class="item-price">
                                RM <?= number_format($item->quantity * $item->price_each, 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="actions">
                <a href="product.php" class="btn btn-continue">
                    Continue Shopping
                </a>
                <a href="order_history.php" class="btn btn-history">
                    View Order History
                </a>
            </div>
        </div>
    </div>
    