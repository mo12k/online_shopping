<?php
// cart/index.php
require '../../_base.php';
$_title = 'Shopping Cart';

// 检查用户是否登录
if (!isset($_SESSION['customer_id'])) {
    temp('error', 'Please login to view your cart');
    redirect('/page/login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
}

$customer_id = $_SESSION['customer_id'];

// 获取购物车内容
$stmt = $db->prepare('
    SELECT ci.*, p.title, p.author, p.price, p.photo_name, p.stock
    FROM cart_item ci
    JOIN cart c ON ci.cart_id = c.cart_id
    JOIN product p ON ci.product_id = p.id
    WHERE c.customer_id = ? AND p.status = 1
    ORDER BY ci.added_at DESC
');
$stmt->execute([$customer_id]);
$cart_items = $stmt->fetchAll();

// 计算总价
$total = 0;
foreach ($cart_items as $item) {
    $total += $item->quantity * $item->price;
}

include '../../_head.php';
?>

<div class="content">
    <h1 style="margin-bottom: 30px;">Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 50px; background: #f9f9f9; border-radius: 10px;">
            <p style="font-size: 18px; color: #666; margin-bottom: 20px;">Your cart is empty</p>
            <a href="../page/shop.php" style="padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;">
                Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 20px;">
            <?php foreach ($cart_items as $item): ?>
                <div style="display: flex; gap: 20px; padding: 20px; background: white; border: 1px solid #eee; border-radius: 10px;">
                    <?php if ($item->photo_name && file_exists("../../upload/{$item->photo_name}")): ?>
                        <img src="../../upload/<?= encode($item->photo_name) ?>" 
                             alt="<?= encode($item->title) ?>"
                             style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                    <?php endif; ?>
                    
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 10px 0;"><?= encode($item->title) ?></h3>
                        <?php if ($item->author): ?>
                            <p style="color: #666; margin: 0 0 10px 0;">by <?= encode($item->author) ?></p>
                        <?php endif; ?>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-weight: bold; color: #e74c3c; font-size: 18px;">
                                    RM <?= number_format($item->price, 0) ?>
                                </span>
                                <span style="color: #666; margin-left: 10px;">
                                    × <?= $item->quantity ?> = RM <?= number_format($item->price * $item->quantity, 0) ?>
                                </span>
                            </div>
                            
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <span style="color: <?= $item->quantity > $item->stock ? '#e74c3c' : '#27ae60' ?>;">
                                    Stock: <?= $item->stock ?>
                                </span>
                                <a href="remove.php?id=<?= $item->product_id ?>" 
                                   style="color: #e74c3c; text-decoration: none; padding: 5px 10px; border: 1px solid #e74c3c; border-radius: 4px;">
                                    Remove
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- 購物車總結 -->
            <div style="background: #f8f9fa; padding: 25px; border-radius: 10px; margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0;">Total: <span style="color: #e74c3c;">RM <?= number_format($total, 0) ?></span></h3>
                        <p style="color: #666; margin: 10px 0 0 0;"><?= count($cart_items) ?> item(s)</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="../page/shop.php" 
                           style="padding: 12px 25px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                            Continue Shopping
                        </a>
                        <a href="../order/checkout.php" 
                           style="padding: 12px 25px; background: #2ecc71; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../../_foot.php'; ?>