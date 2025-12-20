    <?php

    // ============================================================================
    // PHP Setups
    // ============================================================================

    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();

    // ============================================================================
    // General Page Functions
    // ============================================================================

    // Is POST request?
    function is_post() {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    // Obtain GET parameter
    function get($key, $value = null) {
        $value = $_GET[$key] ?? $value;
        return is_array($value) ? array_map('trim', $value) : trim($value);
    }

    // Obtain POST parameter
    function post($key, $value = null) {
        $value = $_POST[$key] ?? $value;
        return is_array($value) ? array_map('trim', $value) : trim($value);
    }

    // Obtain REQUEST (GET and POST) parameter
    function req($key, $value = null) {
        $value = $_REQUEST[$key] ?? $value;
        return is_array($value) ? array_map('trim', $value) : trim($value);
    }

    // Redirect to URL
    function redirect($url = null) {
        $url ??= $_SERVER['REQUEST_URI'];
        header("Location: $url");
        exit();
    }

    // Set or get temporary session variable
    function temp($key, $value = null) {
        if ($value !== null) {
            $_SESSION["temp_$key"] = $value;
        }
        else {
            $value = $_SESSION["temp_$key"] ?? null;
            unset($_SESSION["temp_$key"]);
            return $value;
        }
    }

    // ============================================================================
    // HTML Helpers
    // ============================================================================

    // Encode HTML special characters
    function encode($value) {
        return htmlentities($value);
    }

    function encode_id($id) {
        return rtrim(strtr(base64_encode((string)$id), '+/', '-_'), '=');
    }

    function decode_id($hash) {
        if (!$hash) return 0;

        $hash = strtr($hash, '-_', '+/');
        $id = base64_decode($hash, true);

        return ctype_digit($id) ? (int)$id : 0;
    }

    // Generate <select>
    function html_select($key, $items, $default = '- Select One -', $attr = '', $auto_submit =false) {
        $value = encode($GLOBALS[$key] ?? '');
        $onchange = $auto_submit ?'onchange ="this.form.submit()"' :'';
        

        echo "<select id='$key' name='$key' $attr $onchange>";
        if ($default !== null) {
            echo "<option value=''>$default</option>";
        }
        foreach ($items as $id => $text) {
            $state = ($id.'') ==($value.'')  ? 'selected' : '';
            echo "<option value='$id' $state>$text</option>";
        }
        echo '</select>';
    }

    function html_status_toggle($key, $default = 1) {
    $checked = ($GLOBALS[$key] ?? $default) == 1 ? 'checked' : '';
    echo <<<HTML
    <div style="display:flex; align-items:center; gap:30px; margin:12px 0; font-size:15px; user-select:none;">
        <span style="color:#999;">Unactive</span>
        <label class="toggle-switch">
            <input type="checkbox" name="$key" value="1" $checked>
            <span class="slider round"></span>
        </label>
        <span style="color:#333; font-weight:500;">Active</span>
    </div>
    HTML;
    }

    // ============================================================================
    // Error Handlings
    // ============================================================================

    // Global error array
    $_err = [];

    // Generate <span class='err'>
    function err($key) {
        global $_err;
        if ($_err[$key] ?? false) {
            echo "<span class='err'>$_err[$key]</span>";
        }
        else {
            echo '<span></span>';
        }
    }

    // ===========================================================================
    // Database Setups and Functions
    // ============================================================================

    // Global PDO object
    $_db = new PDO('mysql:dbname=bookstore', 'root', '', [    
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);

    // ============================================================================
    // Global Constants and Variables
    // ============================================================================

    $_category = $_db->query('SELECT category_id, category_name FROM category ORDER BY sort_order')
                 ->fetchAll(PDO::FETCH_KEY_PAIR);

    // ============================================================================
    // Shopping Cart
    // ============================================================================
 
    function update_cart($product_id, $quantity, $customer_id = null, $mode = 'add') {
    global $_db;

    if (!$customer_id && isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];
    }

    $quantity = (int)$quantity;
    if ($quantity < 1 || !$customer_id) return;

    // cart
    $stm = $_db->prepare('SELECT cart_id FROM cart WHERE customer_id = ?');
    $stm->execute([$customer_id]);
    $cart = $stm->fetch();

    if (!$cart) {
        $stm = $_db->prepare('INSERT INTO cart (customer_id) VALUES (?)');
        $stm->execute([$customer_id]);
        $cart_id = $_db->lastInsertId();
    } else {
        $cart_id = $cart->cart_id;
    }

    if ($mode === 'update') {
        
        $stm = $_db->prepare('
            INSERT INTO cart_item (cart_id, product_id, quantity)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)
        ');
        $stm->execute([$cart_id, $product_id, $quantity]);
    } else {
        
        $stm = $_db->prepare('
            INSERT INTO cart_item (cart_id, product_id, quantity)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
        ');
        $stm->execute([$cart_id, $product_id, $quantity]);
    }

    $stm = $_db->prepare('UPDATE cart SET updated_at = NOW() WHERE cart_id = ?');
    $stm->execute([$cart_id]);
}



    function remove_from_cart($product_id, $customer_id = null) {
        global $_db;
        
        if (!$customer_id && isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
        }
        
        if ($customer_id) {
            // call user cart
            $stm = $_db->prepare('SELECT cart_id FROM cart WHERE customer_id = ?');
            $stm->execute([$customer_id]);
            $cart = $stm->fetch();
            
            if ($cart) {
                $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_id = ? AND product_id = ?');
                $stm->execute([$cart->cart_id, $product_id]);
            }
        }
    }

    function get_cart_items($db, $customer_id = null) {
        if (!$customer_id && isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
        }
        
        $cart_data = [];
        
        if ($customer_id) {
            // call user cart and product
            $stm = $db->prepare("
                SELECT ci.cart_item_id, ci.quantity, 
                    p.*, cat.category_name,
                    c.cart_id
                FROM cart c
                JOIN cart_item ci ON c.cart_id = ci.cart_id
                JOIN product p ON ci.product_id = p.id
                LEFT JOIN category cat ON p.category_id = cat.category_id
                WHERE c.customer_id = ?
                ORDER BY ci.added_at DESC
            ");
            $stm->execute([$customer_id]);
            $items = $stm->fetchAll();
            
            foreach ($items as $item) {
                $item->subtotal = $item->price * $item->quantity;
                $cart_data[] = $item;
            }
            
        }
        
        return $cart_data;
    }

    function clear_cart($customer_id = null) {
        global $_db;
        
        if (!$customer_id && isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
        }
        
        if ($customer_id) {
            // call user cart and clear cart
            $stm = $_db->prepare('SELECT cart_id FROM cart WHERE customer_id = ?');
            $stm->execute([$customer_id]);
            $cart = $stm->fetch();
            
            if ($cart) {
                $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_id = ?');
                $stm->execute([$cart->cart_id]);
            }
        } else {
            unset($_SESSION['cart']);
        }
    }

    function get_cart_id($customer_id) {
        global $_db;
        
        if (!$customer_id && isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
        }
        
        if ($customer_id) {
            $stm = $_db->prepare('SELECT cart_id FROM cart WHERE customer_id = ?');
            $stm->execute([$customer_id]);
            $cart = $stm->fetch();
            
            return $cart ? $cart->cart_id : null;
        }
        
        return null;
    }

    function get_cart_total($cart_items) {
        $total = 0;
        foreach ($cart_items as $item) {
            // + subtotal
            if (isset($item->subtotal)) {
                $total += $item->subtotal;
            } 
            // calculate price * quantity
            elseif (isset($item->price) && isset($item->quantity)) {
                $total += $item->price * $item->quantity;
            }
        }
        return $total;
    }

    // Generate <input type='hidden'>
    function html_hidden($key, $attr = '') {
        $value ??= encode($GLOBALS[$key] ?? '');
        echo "<input type='hidden' id='$key' name='$key' value='$value' $attr>";
    }

    
    function create_order($customer_id, $cart_items, $address_id, $payment_method) {
        global $_db;

        try {
            $_db->beginTransaction();

            $stm = $_db->prepare('
                SELECT address, city, state, postcode
                FROM customer_address
                WHERE address_id = ? AND customer_id = ?
            ');
            $stm->execute([$address_id, $customer_id]);
            $addr = $stm->fetch();

            if (!$addr) {
                throw new Exception('Invalid address selected');
            }

            $total_amount = get_cart_total($cart_items);

            $methodMap = [
                'credit_card'      => 'card',
                'debit_card'       => 'card',
                'online_banking'   => 'fpx',
                'cash_on_delivery' => 'ewallet'
            ];

            $db_method = $methodMap[$payment_method] ?? null;
            if (!$db_method) {
                throw new Exception('Invalid payment method');
            }

            $stm = $_db->prepare('
                INSERT INTO orders (
                    customer_id, total_amount, status, order_date, 
                    shipping_address, shipping_city, shipping_state, shipping_postcode
                ) VALUES (?, ?, "pending", NOW(), ?, ?, ?, ?)
            ');

            $stm->execute([
                $customer_id,
                $total_amount,
                $addr->address,
                $addr->city,
                $addr->state,
                $addr->postcode
            ]);

            $order_id = $_db->lastInsertId();

            $stm_item = $_db->prepare('
                INSERT INTO order_item
                (order_id, product_id, quantity, price_each, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ');

            foreach ($cart_items as $item) {

                // check stock
                $stm_check = $_db->prepare('SELECT stock FROM product WHERE id = ?');
                $stm_check->execute([$item->id]);
                $product = $stm_check->fetch();

                if (!$product || $item->quantity > $product->stock) {
                    throw new Exception("Insufficient stock for product ID: {$item->id}");
                }

                $subtotal = $item->price * $item->quantity;

                $stm_item->execute([
                    $order_id,
                    $item->id,
                    $item->quantity,
                    $item->price,
                    $subtotal
                ]);

                // update stock
                $stm_update = $_db->prepare(
                    'UPDATE product SET stock = stock - ? WHERE id = ?'
                );
                $stm_update->execute([$item->quantity, $item->id]);
            }

            $stm = $_db->prepare('
                INSERT INTO payment (order_id, method, status, amount, paid_at)
                VALUES (?, ?, "success", ?, NOW())
            ');
            $stm->execute([$order_id, $db_method, $total_amount]);

            clear_cart($customer_id);

            $_db->commit();
            return $order_id;

        } catch (Exception $e) {
            $_db->rollBack();
            error_log('Order creation failed: ' . $e->getMessage());
            return false;
        }
    }

        function get_customer_addresses($customer_id) {
        global $_db;
        
        $stm = $_db->prepare('
            SELECT address_id, address, city, state, postcode 
            FROM customer_address 
            WHERE customer_id = ? 
            ORDER BY address_id
        ');
        $stm->execute([$customer_id]);
        return $stm->fetchAll();
    }


    function success() {
        global $_db;

        if (!isset($_SESSION['pending_order'])) {
            redirect('checkout.php');
            exit;
        }

        $pending = $_SESSION['pending_order'];

        $customer_id    = $pending['customer_id'];
        $address_id     = $pending['address_id'];
        $payment_method = $pending['payment_method'];

        
        $cart_items = get_cart_items($_db, $customer_id);

        if (empty($cart_items)) {
            temp('error', 'Cart is empty.');
            redirect('cart.php');
            exit;
        }

        $order_id = create_order(
            $customer_id,
            $cart_items,
            $address_id,
            $payment_method
        );

        if (!$order_id) {
            temp('error', 'Failed to create order.');
            redirect('checkout.php');
            exit;
        }

        unset($_SESSION['pending_order']);
        unset($_SESSION['payment_retry']);

        
        redirect('order_confirm.php?id=' . encode_id($order_id));
        exit;
    }


    function fail($message = 'Payment failed.') {

    $_SESSION['payment_retry'] = ($_SESSION['payment_retry'] ?? 0) + 1;

    $MAX_RETRY = 3;

    if ($_SESSION['payment_retry'] >= $MAX_RETRY) {
        temp('error', 'Payment failed too many times. Please checkout again.');

        unset($_SESSION['pending_order']);
        unset($_SESSION['payment_retry']);

        redirect('checkout.php');
        exit;
    }

    
    temp('error', $message);
    redirect();
    exit;
}

function is_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}