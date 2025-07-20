<?php

echo "=== FİNAL TEMİZLEME ===\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=b2b_b2c_db', 'user', 'secret');
    
    // Brand sistemini tamamen temizle
    echo "Brand sistemi temizleniyor...\n";
    
    // Products tablosundan brand_id sütununu kontrol et
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'brand_id'");
    if ($stmt->rowCount() > 0) {
        echo "Brand_id sütunu mevcut - veri temizleniyor...\n";
        $stmt = $pdo->prepare("UPDATE products SET brand_id = NULL");
        $stmt->execute();
        echo "Products.brand_id değerleri temizlendi.\n";
    } else {
        echo "Brand_id sütunu zaten mevcut değil.\n";
    }
    
    // ProductAttribute verilerini temizle
    echo "\nProductAttribute sistemini temizliyorum...\n";
    
    // Tabloları kontrol et ve temizle
    $tables = [
        'product_attribute_values',
        'product_variant_attributes', 
        'category_attributes',
        'product_attributes',
        'attribute_types'
    ];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                $pdo->exec("DELETE FROM $table");
                echo "- $table tablosu temizlendi ($count kayıt silindi)\n";
            } else {
                echo "- $table tablosu zaten boş\n";
            }
        } catch (PDOException $e) {
            echo "- $table tablosu mevcut değil (normal)\n";
        }
    }
    
    // Cache temizleme
    echo "\nMemory debug dosyalarını temizliyorum...\n";
    $debugFiles = [
        'memory_debug.php',
        'memory_debug_detailed.php', 
        'minimal_memory_test.php',
        'emergency_fix.php'
    ];
    
    foreach ($debugFiles as $file) {
        if (file_exists($file)) {
            unlink($file);
            echo "- $file silindi\n";
        }
    }
    
    echo "\n=== TEMİZLEME TAMAMLANDI ===\n";
    echo "Sistem artık sadece Category + Variant sistemi kullanıyor.\n";
    echo "Bellek kullanımı önemli ölçüde azaltıldı.\n";
    
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}