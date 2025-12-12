<?php
// debug_checkout_error.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 禁用 session_start() 重复调用的问题
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION['customer_id'] = 37;

// 先检查错误日志文件权限
echo "<h1>Debug Checkout Error</h1>";

// 1. 检查PHP配置
echo "<h2>PHP Configuration:</h2>";
echo "<p>error_log: " . ini_get('error_log') . "</p>";
echo "<p>display_errors: " . ini_get('display_errors') . "</p>";
echo "<p>log_errors: " . ini_get('log_errors') . "</p>";

// 2. 手动设置错误日志
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/checkout_error.log');

// 3. 包含 base 文件，但在 session_start() 之前
echo "<p>Including _base.php...</p>";

// 重写 session_start 检查
function my_session_start() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return session_start();
    }
    return true;
}

// 临时修改 _base.php 以避免重复 session_start
$base_content = file_get_contents('../_base.php');
$base_content = str_replace('session_start();', 'if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }', $base_content);

// 执行修改后的代码
eval('?>' . $base_content);

echo "<p>_base.php included</p>";

// 4. 设置测试数据
echo "<h2>Setting up test data...</h2>";

// 清空购物车并添加测试商品
clear_cart(37);
update_cart(3, 1, 37); // 添加一个商品

$cart_items = get_cart_items($_db, 37);
echo "<p>Cart items count: " . count($cart_items) . "</p>";

// 获取地址
$stm = $_db->prepare('SELECT address_id FROM customer_address WHERE customer_id = 37 LIMIT 1');
$stm->execute();
$address = $stm->fetch();

if (!$address) {
    echo "<p style='color:red;'>No address found!</p>";
    exit;
}

$address_id = $address->address_id;
echo "<p>Address ID: $address_id</p>";

// 5. 直接测试可能出错的部分
echo "<h2>Testing individual components...</h2>";

// 测试1: 地址验证
try {
    $stm = $_db->prepare('SELECT address_id FROM customer_address WHERE address_id = ? AND customer_id = ?');
    $stm->execute([$address_id, 37]);
    $result = $stm->fetch();
    echo "<p>Address validation: " . ($result ? "✅ PASS" : "❌ FAIL") . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>Address validation error: " . $e->getMessage() . "</p>";
}

// 测试2: 产品库存检查
if ($cart_items) {
    $item = $cart_items[0];
    try {
        $stm = $_db->prepare('SELECT stock, title FROM product WHERE id = ?');
        $stm->execute([$item->id]);
        $product = $stm->fetch();
        echo "<p>Product check: " . ($product ? "✅ FOUND ({$product->title}, Stock: {$product->stock})" : "❌ NOT FOUND") . "</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>Product check error: " . $e->getMessage() . "</p>";
    }
}

// 测试3: SQL语法测试
echo "<h3>Testing SQL syntax...</h3>";

$test_sqls = [
    'INSERT INTO orders (customer_id, address_id, total_amount, status, order_date) VALUES (37, 5, 50.00, "pending", NOW())',
    'INSERT INTO order_item (order_id, product_id, quantity, price_each, subtotal) VALUES (1, 3, 1, 9.99, 9.99)',
    'INSERT INTO payment (order_id, method, status, amount, paid_at) VALUES (1, "credit_card", "completed", 50.00, NOW())',
    'UPDATE orders SET status = "paid" WHERE order_id = 1',
    'UPDATE product SET stock = stock - 1 WHERE id = 3'
];

foreach ($test_sqls as $sql) {
    try {
        // 只测试语法，不执行
        $_db->prepare($sql);
        echo "<p>SQL syntax: ✅ PASS - " . htmlspecialchars(substr($sql, 0, 50)) . "...</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>SQL syntax: ❌ FAIL - " . $e->getMessage() . "</p>";
        echo "<p>SQL: " . htmlspecialchars($sql) . "</p>";
    }
}

// 6. 直接调用函数并捕获输出
echo "<h2>Calling process_simulated_checkout directly...</h2>";

// 创建一个简单的wrapper来捕获错误
function test_checkout() {
    global $_db, $cart_items, $address_id;
    
    try {
        $_db->beginTransaction();
        
        $customer_id = 37;
        $total_amount = 9.99;
        
        // 插入订单
        $stm = $_db->prepare('
            INSERT INTO orders (customer_id, address_id, total_amount, status, order_date) 
            VALUES (?, ?, ?, "pending", NOW())
        ');
        $stm->execute([$customer_id, $address_id, $total_amount]);
        $order_id = $_db->lastInsertId();
        
        echo "<p>Order inserted: $order_id</p>";
        
        // 插入订单项目
        $item = $cart_items[0];
        $subtotal = $item->price * $item->quantity;
        
        $stm = $_db->prepare('
            INSERT INTO order_item (order_id, product_id, quantity, price_each, subtotal) 
            VALUES (?, ?, ?, ?, ?)
        ');
        $stm->execute([$order_id, $item->id, $item->quantity, $item->price, $subtotal]);
        
        echo "<p>Order item inserted</p>";
        
        // 创建支付记录
        $stm = $_db->prepare('
            INSERT INTO payment (order_id, method, status, amount, paid_at) 
            VALUES (?, "credit_card", "completed", ?, NOW())
        ');
        $stm->execute([$order_id, $total_amount]);
        
        echo "<p>Payment record inserted</p>";
        
        // 更新订单状态
        $stm = $_db->prepare('UPDATE orders SET status = "paid" WHERE order_id = ?');
        $stm->execute([$order_id]);
        
        echo "<p>Order status updated</p>";
        
        // 更新库存
        $stm = $_db->prepare('UPDATE product SET stock = stock - ? WHERE id = ?');
        $stm->execute([$item->quantity, $item->id]);
        
        echo "<p>Stock updated</p>";
        
        // 清空购物车
        clear_cart($customer_id);
        echo "<p>Cart cleared</p>";
        
        $_db->commit();
        
        echo "<p style='color:green; font-weight:bold;'>✅ ALL TESTS PASSED!</p>";
        echo "<p>Order ID: $order_id</p>";
        echo "<p><a href='order_confirm.php?id=$order_id'>Test order confirmation</a></p>";
        
        return $order_id;
        
    } catch (Exception $e) {
        $_db->rollBack();
        echo "<p style='color:red; font-weight:bold;'>❌ TEST FAILED: " . $e->getMessage() . "</p>";
        
        $errorInfo = $_db->errorInfo();
        echo "<pre>SQL Error Info: ";
        print_r($errorInfo);
        echo "</pre>";
        
        return false;
    }
}

// 运行测试
$order_id = test_checkout();

// 7. 检查错误日志
echo "<h2>Error Log Contents:</h2>";
$error_log_file = __DIR__ . '/checkout_error.log';
if (file_exists($error_log_file)) {
    $log_content = file_get_contents($error_log_file);
    echo "<pre style='background:#f0f0f0; padding:10px;'>";
    echo htmlspecialchars($log_content);
    echo "</pre>";
} else {
    echo "<p>Error log file not found: $error_log_file</p>";
}

// 8. 检查系统错误日志
echo "<h2>System Error Log (last 20 lines):</h2>";
$system_logs = [
    '/var/log/apache2/error.log',
    '/var/log/httpd/error_log',
    'C:/xampp/apache/logs/error.log',
    'C:/wamp/logs/apache_error.log'
];

foreach ($system_logs as $log_file) {
    if (file_exists($log_file)) {
        echo "<h3>$log_file:</h3>";
        $lines = `tail -20 "$log_file"`;
        echo "<pre style='background:#f0f0f0; padding:10px;'>";
        echo htmlspecialchars($lines);
        echo "</pre>";
        break;
    }
}
?>