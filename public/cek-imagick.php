<?php
echo "GD extension: " . (extension_loaded('gd') ? "AKTIF ✅" : "TIDAK ADA ❌") . "<br>";

if (extension_loaded('gd')) {
    $info = gd_info();
    echo "GD Version: " . $info['GD Version'] . "<br>";
    echo "PNG Support: " . ($info['PNG Support'] ? "YA ✅" : "TIDAK ❌");
}