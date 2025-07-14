<?php
// PHP ayarlarını web server üzerinden kontrol et
echo "<h2>Web Server PHP Upload Ayarları</h2>";
echo "<strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "<br>";
echo "<strong>post_max_size:</strong> " . ini_get('post_max_size') . "<br>";
echo "<strong>max_file_uploads:</strong> " . ini_get('max_file_uploads') . "<br>";
echo "<strong>file_uploads:</strong> " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>";
echo "<strong>memory_limit:</strong> " . ini_get('memory_limit') . "<br>";
echo "<strong>max_execution_time:</strong> " . ini_get('max_execution_time') . "<br>";

// PHP sürümü ve SAPI bilgisi
echo "<hr>";
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
echo "<strong>SAPI:</strong> " . php_sapi_name() . "<br>";

// Tam phpinfo için
echo "<hr>";
echo "<h3>Detaylı PHP Bilgileri:</h3>";
phpinfo();
?> 