<?php
require '../_base.php';

$current = 'customer';

$_title = 'Customer List';
include '../_head.php';
?>
      
<div class="content">

    <h3>Search</h3>

    <input type="text" placeholder="input name" 
           style="padding:8px;width:250px;margin-bottom:20px;border:1px solid #ccc;border-radius:5px;">

    <table>
        <tr>
            <th>Employee Name</th>
            <th>Username</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Function</th>
        </tr>
        <tr>
            <td colspan="5" class="no-data">no data</td>
        </tr>
    </table>

</div>

<?php
include '../_foot.php';




