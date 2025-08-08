#!/bin/bash

echo "🚀 Production Deployment Commands"
echo "================================="

echo "1. Composer update paketleri yükle:"
echo "composer install --no-dev --optimize-autoloader"
echo ""

echo "2. L5Swagger paketini kontrol et:"
echo "composer show darkaonline/l5-swagger"
echo ""

echo "3. Eğer paket yoksa yükle:"
echo "composer require darkaonline/l5-swagger"
echo ""

echo "4. Config cache temizle:"
echo "php artisan config:clear"
echo "php artisan cache:clear"
echo ""

echo "5. Autoloader yeniden yükle:"
echo "composer dump-autoload"
echo ""

echo "6. Config cache'i yeniden oluştur:"
echo "php artisan config:cache"
echo ""

echo "7. Swagger dokümanlarını oluştur:"
echo "php artisan l5-swagger:generate"
echo ""

echo "8. Alternatif çözüm - Swagger'ı geçici devre dışı bırak:"
echo "# config/app.php providers array'inde bu satırı comment out et:"
echo "# L5Swagger\L5SwaggerServiceProvider::class,"
echo ""

echo "💡 Eğer hala sorun devam ederse, L5Swagger tamamen kaldırılabilir:"
echo "composer remove darkaonline/l5-swagger"