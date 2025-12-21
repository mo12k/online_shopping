<?php
require '../_base.php';

admin_require_login();

$current = 'customer';   
$_title = 'Customer List';

// Get input
$keyword = trim(get('keyword') ?? '');

$fields = [
    'customer_id'   => 'ID',
    'username'      => 'Username',
    'email'         => 'Email',
    'is_verified'   => 'Verified',
    'is_blocked'    => 'Status',
    'created_at'    => 'Created At',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'customer_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

//Search function
$sql = "SELECT * FROM customer WHERE 1=1";
$params = [];

if ($keyword !== '') {
    $sql .= " AND (username LIKE ? OR email LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

$sql .= " ORDER BY $sort $dir";



// (2) Paging
$page = req('page', 1);

require_once '../lib/SimplePager.php';
$p = new SimplePager($sql, $params, 10, $page);
$customer_list = $p->result;


$info = temp('info');

include '../_head.php';
?>

<div class="content">

    <form method="get" class="search-form" style="margin-bottom:20px; display:flex; gap:10px;">
        <?= html_search('keyword', 'Search username or email', 'style="padding:8px 12px; border:1px solid #ccc; border-radius:6px; width:250px;"') ?>
        <button style="padding:8px 16px;">Search</button>
    </form>

    <?php if ($info): ?>
        <div class="alert-success-fixed">
            <div class="alert-content">
                <strong>Success!</strong> <?= encode($info) ?>
                <span class="alert-close">×</span>
            </div>
        </div>
        <?php endif; ?>
        <p style="margin:20px 0; color:#666; font-size:15px;">
           <?= $p->count ?> of <?= $p->item_count ?> record(s) |
    Page <?= $p->page ?> of <?= $p->page_count ?>
        </p>
        
    <table class="customers-table">
        <thead>
            <tr>
                <?= table_headers($fields, $sort, $dir, "page=$page") ?>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($customer_list)): ?>
                <tr><td colspan="5" style="text-align:center; padding:20px;">No results found</td></tr>
            <?php else: ?>
                <?php foreach ($customer_list as $customer): ?>
                    <tr>
                        <td><?= $customer->customer_id ?></td>
                        <td><?= $customer->username ?></td>
                        <td><?= $customer->email ?></td>
                        <td><?= $customer->is_verified ? 'Yes' : 'No' ?></td>
                        <td>
                            <?php
                                $is_blocked = isset($customer->is_blocked) && (int)$customer->is_blocked === 1;
                                echo $is_blocked ? 'Blocked' : 'Active';
                            ?>
                        </td>
                        <td><?= $customer->created_at ?></td>
                        <td>
                            <?php if (isset($customer->is_blocked) && (int)$customer->is_blocked === 1): ?>
                                <form method="post" action="unblock.php?sort=<?= encode($sort) ?>&dir=<?= encode($dir) ?>&page=<?= (int)$page ?>&keyword=<?= encode($keyword) ?>" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= (int)$customer->customer_id ?>">
                                    <button type="submit">Unblock</button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="block.php?sort=<?= encode($sort) ?>&dir=<?= encode($dir) ?>&page=<?= (int)$page ?>&keyword=<?= encode($keyword) ?>" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= (int)$customer->customer_id ?>">
                                    <button type="submit" onclick="return confirm('Block this customer?');">Block</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paging -->
    <div style="margin-top:20px;">
        <?= $p->html("sort=$sort&dir=$dir") ?>
    </div>



</div>

<?php include '../_foot.php'; ?>
