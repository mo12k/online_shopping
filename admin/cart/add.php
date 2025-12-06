<?php
// cart/add.php
require '../../_base.php';
$_title = 'Add to Cart';

// 检查用户是否登录
if (!isset($_SESSION['customer_id'])) {
    temp('error', 'Please login to add items to cart');
    redirect('/page/login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
}

$customer_id = $_SESSION['customer_id'];

// 获取参数
$product_id = get('id');
$quantity = get('quantity', 1);

if (!$product_id) {
    temp('error', 'Product ID is required');
    redirect('../page/shop.php');
}

// 验证产品是否存在且有库存
$stm = $db->prepare('SELECT * FROM product WHERE id = ? AND status = 1 AND stock > 0');
$stm->execute([$product_id]);
$product = $stm->fetch();

if (!$product) {
    temp('error', 'Product not found or out of stock');
    redirect('../page/shop.php');
}

// 验证数量
$quantity = intval($quantity);
if ($quantity < 1) {
    $quantity = 1;
}
if ($quantity > $product->stock) {
    $quantity = $product->stock;
}

try {
    // 检查购物车是否存在
    $stmt = $db->prepare('SELECT cart_id FROM cart WHERE customer_id = ?');
    $stmt->execute([$customer_id]);
    $cart = $stmt->fetch();
    
    if (!$cart) {
        // 创建购物车
        $stmt = $db->prepare('INSERT INTO cart (customer_id) VALUES (?)');
        $stmt->execute([$customer_id]);
        $cart_id = $db->lastInsertId();
    } else {
        $cart_id = $cart->cart_id;
    }
    
    // 检查商品是否已经在购物车中
    $stmt = $db->prepare('SELECT quantity FROM cart_item WHERE cart_id = ? AND product_id = ?');
    $stmt->execute([$cart_id, $product_id]);
    $existing_item = $stmt->fetch();
    
    if ($existing_item) {
        // 更新数量
        $new_quantity = $existing_item->quantity + $quantity;
        $new_quantity = min($product->stock, $new_quantity); // 不能超过库存
        
        $stmt = $db->prepare('UPDATE cart_item SET quantity = ? WHERE cart_id = ? AND product_id = ?');
        $stmt->execute([$new_quantity, $cart_id, $product_id]);
        
        $message = "Updated quantity in cart";
    } else {
        // 添加新商品
        $stmt = $db->prepare('INSERT INTO cart_item (cart_id, product_id, quantity) VALUES (?, ?, ?)');
        $stmt->execute([$cart_id, $product_id, $quantity]);
        
        $message = "Added to cart";
    }
    
    temp('success', $message);
    
    // 返回到来页面或购物车页面
    $return_url = get('return');
    if ($return_url) {
        redirect($return_url);
    } else {
        redirect('index.php');
    }
    
} catch (Exception $e) {
    temp('error', 'Failed to add to cart: ' . $e->getMessage());
    redirect('../page/shop.php');
}