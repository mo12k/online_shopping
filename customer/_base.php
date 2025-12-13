    <?php

    // ============================================================================
    // PHP Setups
    // ============================================================================

    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();

    // ============================================================================
    // General Page Functions
    // ============================================================================

    // Is GET request?
    function is_get() {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

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

    function html_textarea($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
    }
    
    // Generate <input type='text'>
    function html_text($key, $attr = '') {
        $value = encode($GLOBALS[$key] ?? '');
        echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
    }


    // Generate <input type='number'>
    function html_number($key, $min = '', $max = '', $step = '', $attr = '') {
        $value = encode($GLOBALS[$key] ?? '');
        echo "<input type='number' id='$key' name='$key' value='$value'
                    min='$min' max='$max' step='$step' $attr>";
    }
    // Generate <input type='search'>
    function html_search($key, $placeholder ,$attr = '') {
        $value = encode($GLOBALS[$key] ?? '');
        $ph = $placeholder ? "placeholder='$placeholder'" : '';
        echo "<input type='search' id='$key' name='$key' value='$value' $ph $attr>";
    }

    // Generate <input type='radio'> list
    function html_radios($key, $items, $br = false) {
        $value = encode($GLOBALS[$key] ?? '');
        echo '<div>';
        foreach ($items as $id => $text) {
            $state = $id == $value ? 'checked' : '';
            echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
            if ($br) {
                echo '<br>';
            }
        }
        echo '</div>';
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

    // Generate <input type='file'>
    function html_file($key, $accept = '', $attr = '') {
        echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
    }

    function get_file($key) {
        $f = $_FILES[$key] ?? null;
        
        if ($f && $f['error'] == 0) {
            return (object)$f;
        }

        return null;
    }

    // Crop, resize and save photo
    function save_photo($f, $folder) {
        $photo = uniqid() . '.jpg';
        move_uploaded_file($f->tmp_name, "$folder/$photo");

        return $photo;
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


    // Is money?
    function is_money($value) {
        return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
    }



function table_headers($fields, $sort, $dir, $href = '') {
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class
        
        // TODO
        if($k ==$sort){
            $d = $dir =='asc'?'desc':'asc';
            $c = $dir;

        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
    }
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



    // ============================================================================
    // Database Setups and Functions
    // ============================================================================


    // Global PDO object
    $_db = new PDO('mysql:dbname=bookstore', 'root', '', [    
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);

    // Is unique?
    function is_unique($value, $table, $field) {
        global $_db;
        $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
        $stm->execute([$value]);
        return $stm->fetchColumn() == 0;
    }

    // Is exists?
    function is_exists($value, $table, $field) {
        global $_db;
        $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
        $stm->execute([$value]);
        return $stm->fetchColumn() > 0;
    }



    // ============================================================================
    // Global Constants and Variables
    // ============================================================================

    $_genders = [
        'F' => 'Female',
        'M' => 'Male',
    ];

    $_category = $_db->query('SELECT category_id, category_name FROM category ORDER BY sort_order')
                 ->fetchAll(PDO::FETCH_KEY_PAIR);

    $_status = [1 => 'Published', 0 => 'Draft'];

  
    // ============================================================================
    // Shopping Cart
    // ============================================================================
 
    

        function update_cart($product_id, $quantity, $customer_id = null) {
        global $_db;
        
        if (!$customer_id && isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
        }
        
        $quantity = (int)$quantity;
        
        if ($customer_id) {
            // 1. 确保用户有购物车
            $stm = $_db->prepare('SELECT cart_id FROM cart WHERE customer_id = ?');
            $stm->execute([$customer_id]);
            $cart = $stm->fetch();
            
            if (!$cart) {
                // 创建新的购物车
                $stm = $_db->prepare('INSERT INTO cart (customer_id) VALUES (?)');
                $stm->execute([$customer_id]);
                $cart_id = $_db->lastInsertId();
            } else {
                $cart_id = $cart->cart_id;
            }
            
            // 2. 更新购物车商品
            if ($quantity > 0) {
                $stm = $_db->prepare('
                    INSERT INTO cart_item (cart_id, product_id, quantity) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE quantity = ?
                ');
                $stm->execute([$cart_id, $product_id, $quantity, $quantity]);
            } else {
                // 数量为0，删除商品
                $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_id = ? AND product_id = ?');
                $stm->execute([$cart_id, $product_id]);
            }
            
            // 3. 更新购物车更新时间
            $stm = $_db->prepare('UPDATE cart SET updated_at = NOW() WHERE cart_id = ?');
            $stm->execute([$cart_id]);
            
        } else {
            // 游客使用Session
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
            
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }
        }
    }

    function remove_from_cart($product_id, $customer_id = null) {
        global $_db;
        
        if (!$customer_id && isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
        }
        
        if ($customer_id) {
            // 获取用户的购物车
            $stm = $_db->prepare('SELECT cart_id FROM cart WHERE customer_id = ?');
            $stm->execute([$customer_id]);
            $cart = $stm->fetch();
            
            if ($cart) {
                $stm = $_db->prepare('DELETE FROM cart_item WHERE cart_id = ? AND product_id = ?');
                $stm->execute([$cart->cart_id, $product_id]);
            }
        } else {
            // 游客
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
            }
            
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }
        }
    }

    function get_cart_items($db, $customer_id = null) {
        if (!$customer_id && isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
        }
        
        $cart_data = [];
        
        if ($customer_id) {
            // 获取用户的购物车及商品
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
            
        } elseif (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            // 游客从Session获取
            $product_ids = array_keys($_SESSION['cart']);
            if (!empty($product_ids)) {
                $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
                $stm = $db->prepare("
                    SELECT p.*, c.category_name 
                    FROM product p 
                    LEFT JOIN category c ON p.category_id = c.category_id 
                    WHERE p.id IN ($placeholders)
                ");
                $stm->execute($product_ids);
                $products = $stm->fetchAll();
                
                foreach ($products as $product) {
                    $product->cart_item_id = null;
                    $product->cart_id = null;
                    $product->quantity = $_SESSION['cart'][$product->id];
                    $product->subtotal = $product->price * $product->quantity;
                    $cart_data[] = $product;
                }
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
            // 获取用户的购物车并清空所有商品
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

    // 新增：获取用户的购物车ID
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

    
    function create_order($customer_id, $cart_items, $address_id, $payment_method) {  // 改为 $cart_items
    global $_db;
    
    try {
        $_db->beginTransaction();
        
        // validate address
        $stm = $_db->prepare('SELECT address_id FROM customer_address WHERE address_id = ? AND customer_id = ?');
        $stm->execute([$address_id, $customer_id]);
        $address = $stm->fetch();
        
        if (!$address) {
            throw new Exception('Invalid address selected');
        }
        
        
        $total_amount = get_cart_total($cart_items);  
        
        $methodMap = [
            'credit_card'       => 'card',
            'debit_card'        => 'card',
            'online_banking'    => 'fpx',
            'cash_on_delivery'  => 'ewallet'
        ];

        $db_method = $methodMap[$payment_method] ?? null;
        if (!$db_method) {
            throw new Exception('Invalid payment method');
        }
        
        $stm = $_db->prepare('
            INSERT INTO orders (customer_id, address_id, total_amount, status, order_date) 
            VALUES (?, ?, ?, "pending", NOW())
        ');
        $stm->execute([$customer_id, $address_id, $total_amount]);
        $order_id = $_db->lastInsertId();
        
        
        $stm = $_db->prepare('
            INSERT INTO order_item (order_id, product_id, quantity, price_each, subtotal) 
            VALUES (?, ?, ?, ?, ?)
        ');
        
        foreach ($cart_items as $item) { 
            // validate stock
            $stm_check = $_db->prepare('SELECT stock FROM product WHERE id = ?');
            $stm_check->execute([$item->id]);
            $product = $stm_check->fetch();
            
            if (!$product || $item->quantity > $product->stock) {
                throw new Exception("Insufficient stock for product ID: {$item->id}");
            }

            $subtotal = $item->price * $item->quantity;
            $stm->execute([$order_id, $item->id, $item->quantity, $item->price, $subtotal]);
            
            // update stock
            $stm_update = $_db->prepare('UPDATE product SET stock = stock - ? WHERE id = ?');
            $stm_update->execute([$item->quantity, $item->id]);
        }
        
        // create payment
        $stm = $_db->prepare('
            INSERT INTO payment (order_id, method, status, amount, paid_at) 
            VALUES (?, ?, "success", ?, NOW())
        ');
        $stm->execute([$order_id, $db_method, $total_amount]);
        
        // clear
        clear_cart($customer_id);
        
        $_db->commit();
        return $order_id;
        
    } catch (Exception $e) {
        $_db->rollBack();
        error_log("Order creation failed: " . $e->getMessage());
        return false;
    }
}

    // 获取客户的地址列表
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

    // 获取单个地址详情
    function get_address_by_id($address_id, $customer_id = null) {
        global $_db;
        
        $sql = 'SELECT * FROM customer_address WHERE address_id = ?';
        $params = [$address_id];
        
        if ($customer_id) {
            $sql .= ' AND customer_id = ?';
            $params[] = $customer_id;
        }
        
        $stm = $_db->prepare($sql);
        $stm->execute($params);
        return $stm->fetch();
    }
    
function get_order_by_id($order_id, $customer_id = null) {
    global $_db;
    
    $sql = 'SELECT o.*, ca.address, ca.city, ca.state, ca.postcode 
            FROM orders o 
            JOIN customer_address ca ON o.address_id = ca.address_id 
            WHERE o.order_id = ?';
    
    $params = [$order_id];
    
    if ($customer_id) {
        $sql .= ' AND o.customer_id = ?';
        $params[] = $customer_id;
    }
    
    $stm = $_db->prepare($sql);
    $stm->execute($params);
    return $stm->fetch();
}

/**
 * 获取订单商品
 */
function get_order_items($order_id) {
    global $_db;
    
    $stm = $_db->prepare('
        SELECT oi.*, p.title, p.photo_name,
               oi.price_each as price 
        FROM order_item oi 
        JOIN product p ON oi.product_id = p.id 
        WHERE oi.order_id = ? 
        ORDER BY oi.order_item_id
    ');
    $stm->execute([$order_id]);
    return $stm->fetchAll();
}




   