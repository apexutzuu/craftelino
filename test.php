<?php
echo "<h2>PHP functioneaza!</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server path: " . __DIR__ . "</p>";
echo "<p>Config exists: " . (file_exists('config.php') ? 'DA' : 'NU') . "</p>";
echo "<p>Admin config exists: " . (file_exists('admin/config.php') ? 'DA' : 'NU') . "</p>";
echo "<p>Install folder exists: " . (is_dir('install') ? 'DA' : 'NU') . "</p>";
echo "<p>System folder exists: " . (is_dir('system') ? 'DA' : 'NU') . "</p>";
echo "<p>Catalog folder exists: " . (is_dir('catalog') ? 'DA' : 'NU') . "</p>";

echo "<h3>Fisiere lipsa:</h3><ul>";
$check = [
    'system/startup.php',
    'system/framework.php',
    'system/engine/action.php',
    'system/engine/controller.php',
    'system/engine/loader.php',
    'system/engine/registry.php',
    'system/engine/router.php',
    'install/index.php',
    'install/controller/common/home.php',
    'catalog/controller/common/home.php',
];
foreach ($check as $f) {
    $status = file_exists($f) ? '&#10004; OK' : '&#10008; LIPSESTE';
    $color = file_exists($f) ? 'green' : 'red';
    echo "<li style='color:$color'>$f - $status</li>";
}
echo "</ul>";

echo "<h3>Permisiuni directoare:</h3><ul>";
$dirs = [
    'system/storage',
    'system/storage/cache',
    'system/storage/logs',
    'system/storage/session',
    'system/storage/upload',
    'system/storage/modification',
    'image',
    'image/cache',
    'image/catalog',
];
foreach ($dirs as $d) {
    if (is_dir($d)) {
        $perms = substr(sprintf('%o', fileperms($d)), -4);
        $writable = is_writable($d) ? 'Writable' : 'NOT Writable';
        $color = is_writable($d) ? 'green' : 'red';
        echo "<li style='color:$color'>$d - $perms - $writable</li>";
    } else {
        echo "<li style='color:red'>$d - DIRECTOR LIPSESTE</li>";
    }
}
echo "</ul>";
