<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 开始输出缓冲
ob_start();

require '../_base.php';

// 直接显示调试信息（临时）
echo '<pre style="background:#f0f0f0; padding:10px; margin:10px;">';
echo "=== CHECKOUT DEBUG ===\n";
echo "Current file: " . __FILE__ . "\n";
echo "Current dir: " . __DIR__ . "\n";

// 检查 order_confirm.php 是否存在
$confirm_file = __DIR__ . '/order_confirm.php';
echo "Looking for: " . $confirm_file . "\n";
echo "File exists: " . (file_exists($confirm_file) ? 'YES' : 'NO') . "\n";

// 检查 session
echo "Session customer_id: " . (isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 'NOT SET') . "\n";
echo "</pre>";


include '../../_head.php';
include '../../_header.php';
$current = 'checkout';
$_title = 'Checkout';

// 检查登录
if (!isset($_SESSION['customer_id'])) {
    temp('info', 'Please login to checkout');
    redirect('../../page/login.php');
}

$customer_id = $_SESSION['customer_id'];

// 获取购物车
$cart_items = get_cart_items($_db, $customer_id);
if (empty($cart_items)) {
    temp('error', 'Your cart is empty');
    redirect('cart.php');
}

// 获取客户地址
$addresses = get_customer_addresses($customer_id);

// 处理结账
// 在 checkout.php 的 POST 处理部分修改：

if (is_post()) {
    $address_id = req('address_id');
    $payment_method = req('payment_method');
    
    // 添加调试信息
    echo '<pre style="background:#ffebee; padding:20px; margin:20px;">';
    echo "=== CHECKOUT POST DEBUG ===\n";
    echo "Address ID: " . ($address_id ?: 'EMPTY') . "\n";
    echo "Payment Method: " . ($payment_method ?: 'EMPTY') . "\n";
    echo "Customer ID: $customer_id\n";
    echo "Cart items count: " . count($cart_items) . "\n";
    
    if (empty($address_id)) {
        echo "ERROR: No address selected\n";
        echo "</pre>";
        temp('error', 'Please select a delivery address');
        redirect();
    }
    
    if (empty($payment_method)) {
        echo "ERROR: No payment method selected\n";
        echo "</pre>";
        temp('error', 'Please select a payment method');
        redirect();
    }
    
    echo "Calling process_simulated_checkout...\n";
    echo "</pre>";
    
    // 模拟支付处理 - 这里需要确保真正创建订单
    $order_id = process_simulated_checkout($customer_id, $cart_items, $address_id, $payment_method);
    
    // 添加更多调试
    echo '<pre style="background:#e8f5e8; padding:20px; margin:20px;">';
    echo "=== ORDER CREATION RESULT ===\n";
    echo "process_simulated_checkout returned: " . ($order_id ? "Order ID: $order_id" : "FALSE") . "\n";
    
    if ($order_id) {
        // 验证订单是否真的创建了
        global $_db;
        $stm = $_db->prepare('SELECT * FROM orders WHERE order_id = ? AND customer_id = ?');
        $stm->execute([$order_id, $customer_id]);
        $order = $stm->fetch();
        
        if ($order) {
            echo "✅ Order verified in database!\n";
            echo "Order details:\n";
            print_r($order);
            
            // 检查订单项目
            $stm = $_db->prepare('SELECT COUNT(*) FROM order_item WHERE order_id = ?');
            $stm->execute([$order_id]);
            $item_count = $stm->fetchColumn();
            echo "Order items count: $item_count\n";
            
            // 检查支付记录
            $stm = $_db->prepare('SELECT * FROM payment WHERE order_id = ?');
            $stm->execute([$order_id]);
            $payment = $stm->fetch();
            echo "Payment record: " . ($payment ? "FOUND" : "NOT FOUND") . "\n";
        } else {
            echo "❌ Order NOT FOUND in database!\n";
        }
    } else {
        echo "❌ Order creation FAILED!\n";
        
        // 检查数据库错误
        echo "Database error info:\n";
        print_r($_db->errorInfo());
    }
    
    echo "</pre>";
    
    // 临时：不重定向，显示测试链接
    if ($order_id) {
        echo '<div style="background:#d4edda; padding:20px; margin:20px; border:1px solid #c3e6cb;">';
        echo '<h3>✅ Order Created Successfully!</h3>';
        echo '<p>Order ID: <strong>' . $order_id . '</strong></p>';
        echo '<p><a href="order_confirm.php?id=' . $order_id . '" style="font-size:18px; color:blue;">Click here to view Order Confirmation</a></p>';
        echo '</div>';
        
        // 临时注释掉重定向
        // temp('success', "Order #$order_id placed successfully!");
        // redirect("order_confirm.php?id=$order_id");
    } else {
        echo '<div style="background:#f8d7da; padding:20px; margin:20px; border:1px solid #f5c6cb;">';
        echo '<h3>❌ Order Creation Failed</h3>';
        echo '<p>The order could not be created. Please check the debug information above.</p>';
        echo '</div>';
        
        // temp('error', 'Failed to create order. Please try again.');
        // redirect();
    }
    
    exit(); // 停止执行，查看调试信息

    
    function manually_create_order($customer_id, $cart_items, $address_id, $payment_method) {
    global $_db;
    
    echo '<pre style="background:#fff3cd; padding:20px;">';
    echo "=== MANUALLY CREATING ORDER ===\n";
    
    try {
        // 开始事务
        $_db->beginTransaction();
        
        // 1. 计算总金额
        $total_amount = 0;
        foreach ($cart_items as $item) {
            $total_amount += $item->price * $item->quantity;
        }
        
        echo "Total amount: RM " . number_format($total_amount, 2) . "\n";
        
        // 2. 创建订单
        $stm = $_db->prepare('
            INSERT INTO orders (customer_id, address_id, total_amount, status, order_date) 
            VALUES (?, ?, ?, "pending", NOW())
        ');
        $stm->execute([$customer_id, $address_id, $total_amount]);
        $order_id = $_db->lastInsertId();
        
        echo "Order created with ID: $order_id\n";
        
        // 3. 添加订单项目
        $stm = $_db->prepare('
            INSERT INTO order_item (order_id, product_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ');
        
        foreach ($cart_items as $item) {
            $stm->execute([$order_id, $item->id, $item->quantity, $item->price]);
            echo "Added item: {$item->title} × {$item->quantity} @ RM {$item->price}\n";
            
            // 更新库存
            $update_stm = $_db->prepare('UPDATE product SET stock = stock - ? WHERE id = ?');
            $update_stm->execute([$item->quantity, $item->id]);
        }
        
        // 4. 创建支付记录
        $simulated_ref = 'SIM_' . time() . '_' . rand(1000, 9999);
        $stm = $_db->prepare('
            INSERT INTO payment (order_id, method, status, amount, paid_at, payment_reference) 
            VALUES (?, ?, "completed", ?, NOW(), ?)
        ');
        $stm->execute([$order_id, $payment_method, $total_amount, $simulated_ref]);
        echo "Payment record created\n";
        
        // 5. 更新订单状态为已支付
        $stm = $_db->prepare('UPDATE orders SET status = "paid" WHERE order_id = ?');
        $stm->execute([$order_id]);
        echo "Order status updated to 'paid'\n";
        
        // 6. 清空购物车
        clear_cart($customer_id);
        echo "Cart cleared\n";
        
        // 提交事务
        $_db->commit();
        
        echo "✅ Order creation COMPLETED successfully!\n";
        echo "</pre>";
        
        return $order_id;
        
    } catch (Exception $e) {
        $_db->rollBack();
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        echo "</pre>";
        return false;
    }
}
}
?>

<style>
/* 保持你现有的CSS样式不变 */
.checkout-container {
    min-width: 500px;
    max-width: 1000px;
    margin: 40px auto;
    padding: 0px 20px;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-top: 30px;
}

.order-summary, .checkout-form {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.form-section {
    margin-bottom: 30px;
}

.form-section h3 {
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
}

.address-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.address-option input[type="radio"] {
    margin: 0;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.address-option {
    display: flex;
    gap: 15px;
    padding: 10px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.address-option:hover {
    border-color: #6d4c41;
}

.address-option input[type="radio"]:checked + .address-details {
    background: #f9f5f2;
}

.address-details {
     display: flex;
     width: 500px;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
}

.payment-option:hover {
    border-color: #6d4c41;
}

.payment-option input[type="radio"] {
    margin: 0;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.order-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.order-total {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #e0e0e0;
    font-size: 18px;
    text-align: right;
}

.btn-checkout {
    width: 100%;
    padding: 16px;
    background: #6d4c41;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-checkout:hover {
    background: #5a3e34;
    transform: translateY(-2px);
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

/* 虚拟信用卡表单 */
.virtual-card-form {
    display: none;
    margin-top: 20px;
    padding: 20px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}
</style>

<div class="checkout-container">
    <h1>Checkout</h1>
    
    <div class="checkout-content">
        <!-- 订单摘要 -->
        <div class="order-summary">
            <h2>Order Summary</h2>
            <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <span class="item-name"><?= encode($item->title) ?> × <?= $item->quantity ?></span>
                    <span class="item-price">RM <?= number_format($item->subtotal, 2) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="order-total">
                <strong>Total: RM <?= number_format(get_cart_total($cart_items), 2) ?></strong>
            </div>
        </div>
        
        <!-- 结账表单 -->
        <div class="checkout-form">
            <form method="post" id="checkout-form">
                <!-- 选择地址 -->
                <div class="form-section">
                    <h3>Delivery Address</h3>
                    <?php if (empty($addresses)): ?>
                        <div class="alert alert-warning">
                            No address found. <a href="add_address.php">Add a new address</a>
                        </div>
                    <?php else: ?>
                        <div class="address-list">
                            <?php foreach ($addresses as $address): ?>
                                <label class="address-option">
                                    <input type="radio" name="address_id" value="<?= $address->address_id ?>" required>
                                    <div class="address-details">
                                        <strong><?= encode($address->address) ?></strong>, <?= encode($address->city) ?>, <?= encode($address->state) ?>, <?= encode($address->postcode) ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- 支付方式 -->
                <div class="form-section">
                    <h3>Payment Method</h3>
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="credit_card" required>
                            <span>Credit Card (Simulated)</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="debit_card">
                            <span>Debit Card (Simulated)</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="online_banking">
                            <span>Online Banking (Simulated)</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cash_on_delivery">
                            <span>Cash on Delivery</span>
                        </label>
                    </div>
                    
                    <!-- 虚拟信用卡表单（可选显示） -->
                    <div id="virtual-card-form" class="virtual-card-form">
                        <h4>Test Card Information</h4>
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" value="4242 4242 4242 4242">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" value="12/30">
                            </div>
                            <div class="form-group">
                                <label>CVC</label>
                                <input type="text" value="123">
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-checkout" id="submit-btn">
                    Place Order
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// 显示/隐藏虚拟信用卡表单
$(document).ready(function() {
    $('input[name="payment_method"]').on('change', function() {
        const method = $(this).val();
        const $cardForm = $('#virtual-card-form');
        
        if (method === 'credit_card' || method === 'debit_card') {
            $cardForm.slideDown(300);
        } else {
            $cardForm.slideUp(300);
        }
    });
    
    // 为模拟支付添加一些效果
    $('#checkout-form').on('submit', function(e) {
        // 可以添加一些模拟延迟或动画
        $('#submit-btn').html('<span class="spinner"></span> Processing...');
        $('#submit-btn').prop('disabled', true);
        
        // 模拟网络延迟
        setTimeout(function() {
            $('#submit-btn').html('Processing Payment...');
        }, 1000);
        
        // 表单会正常提交
        return true;
    });
});
</script>

<style>
.spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #fff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 8px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<?php include '../../_footer.php'; ?>