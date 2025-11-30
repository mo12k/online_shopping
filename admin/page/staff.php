<?php
require '../_base.php';
$current = 'staff';
$_title = 'Staff List';
include '../_head.php';
?>
      
<button data-get="/">Staff</button>
<button data-get="/page/demo1.php">Demo 1</button>
<button data-get="demo1.php">Demo 1</button>
<button data-get>Reload</button>
<span data-get="https://www.tarc.edu.my">TAR UMT</span>

<?php
include '../_foot.php';