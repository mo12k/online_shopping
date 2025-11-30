<?php
// rename_photos.php - 放在 upload 資料夾執行即可
$folder = './';  // 目前資料夾
$files = glob($folder . '{*.jpg,*.jpeg,*.png,*.gif,*.webp}', GLOB_BRACE);

if (empty($files)) {
    die("資料夾裡沒有圖片！\n");
}

sort($files); // 照檔名排序（可選）
$counter = 1;

foreach ($files as $file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $new_name = "product" . $counter . ".jpg" ;
    
    if ($file !== $new_name) {
        if (rename($file, $new_name)) {
            echo "成功：$file → $new_name\n";
        } else {
            echo "失敗：$file\n";
        }
    } else {
        echo "跳過：$new_name 已經存在\n";
    }
    $counter++;
}

echo "\n全部完成！共處理 " . ($counter - 1) . " 張圖片\n";
echo "現在可以刪除這個 rename_photos.php 檔案了！\n";
?>