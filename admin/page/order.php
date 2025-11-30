<?php
require '../_base.php';
$current = 'order';
$_title = 'Order List';
include '../_head.php';
?>
      
<button data-get="/">Order</button>
<button data-get="/page/demo1.php">Demo 1</button>
<button data-get="demo1.php">Demo 1</button>
<button data-get>Reload</button>
<span data-get="https://www.tarc.edu.my">TAR UMT</span>

<?php
include '../_foot.php';