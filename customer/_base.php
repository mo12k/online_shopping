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
        
        // for guest
        if (!$customer_id && isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
        }
        
        $quantity = (int)$quantity;
        
        if ($customer_id) {
            // for member
            if ($quantity > 0) {
                
                $stm = $_db->prepare('
                    INSERT INTO cart (customer_id, product_id, quantity) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE quantity = ?
                ');
                $stm->execute([$customer_id, $product_id, $quantity, $quantity]);
            } else {
                // delete cart when quantity 0 <= 
                $stm = $_db->prepare('DELETE FROM cart WHERE customer_id = ? AND product_id = ?');
                $stm->execute([$customer_id, $product_id]);
            }
        } else {
            // for guest use session
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
            $stm = $_db->prepare('DELETE FROM cart WHERE customer_id = ? AND product_id = ?');
            $stm->execute([$customer_id, $product_id]);
        } else {
            // for guest
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
            // get from database when is member
            $stm = $db->prepare("
                SELECT c.cart_id, c.product_id, c.quantity, 
                    p.*, cat.category_name 
                FROM cart c
                JOIN product p ON c.product_id = p.id
                LEFT JOIN category cat ON p.category_id = cat.category_id
                WHERE c.customer_id = ?
            ");
            $stm->execute([$customer_id]);
            $cart_data = $stm->fetchAll();
            
        } elseif (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            // get from session when is guest
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
                    $product->cart_id = null; // session won't save in database, do not have cart_id
                    $product->quantity = $_SESSION['cart'][$product->id];
                    $product->subtotal = $product->price * $product->quantity;
                    $cart_data[] = $product;
                }
            }
        }
        
        return $cart_data;
    }

    function get_cart_total($items) {
        $total = 0;
        foreach ($items as $item) {
            // 如果已经有 subtotal 属性，直接使用
            if (isset($item->subtotal)) {
                $total += $item->subtotal;
            } 
            // 否则计算 price * quantity
            elseif (isset($item->price) && isset($item->quantity)) {
                $total += $item->price * $item->quantity;
            }
            // 如果是数组格式
            elseif (is_array($item) && isset($item['price']) && isset($item['quantity'])) {
                $total += $item['price'] * $item['quantity'];
            }
        }
        return $total;
    }

    function clear_cart($customer_id = null) {
        global $_db;
        
        if (!$customer_id && isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
        }
        
        if ($customer_id) {
            $stm = $_db->prepare('DELETE FROM cart WHERE customer_id = ?');
            $stm->execute([$customer_id]);
        } else {
            unset($_SESSION['cart']);
        }
    }

    // Generate <input type='hidden'>
    function html_hidden($key, $attr = '') {
        $value ??= encode($GLOBALS[$key] ?? '');
        echo "<input type='hidden' id='$key' name='$key' value='$value' $attr>";
    }
    
   