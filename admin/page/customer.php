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

$stm = $_db->prepare($sql);
$stm->execute($params);
$customer_list = $stm->fetchAll();

include '../_head.php';
?>

<div class="content">

    <form method="get" class="search-form" style="margin-bottom:20px; display:flex; gap:10px;">
        <input type="text" name="keyword" placeholder="Search username or email"
               value="<?= $keyword ?>"
               style="padding:8px 12px; border:1px solid #ccc; border-radius:6px; width:250px;">
        <button style="padding:8px 16px;">Search</button>
    </form>

    <p><?= count($customer_list) ?> record(s)</p>

    <table class="customers-table">
        <thead>
            <tr>
                <?= table_headers($fields, $sort, $dir) ?>
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
                        <td><?= $customer->created_at ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>


</div>

<?php include '../_foot.php'; ?>
