<?php

return [
    // API Tag Descriptions
    'tags' => [
        'authentication' => 'Kimlik Doğrulama',
        'authentication_description' => 'Kullanıcı kimlik doğrulama API uç noktaları',
        
        'users' => 'Kullanıcılar',
        'users_description' => 'Kullanıcı profili ve hesap yönetimi API uç noktaları',
        
        'wishlist' => 'İstek Listesi',
        'wishlist_description' => 'Kullanıcı istek listesi yönetimi API uç noktaları',
        
        'products' => 'Ürünler',
        'products_description' => 'Ürün kataloğu ve arama API uç noktaları',
        
        'categories' => 'Kategoriler',
        'categories_description' => 'Ürün kategorileri API uç noktaları',
        
        'cart' => 'Sepet',
        'cart_description' => 'Alışveriş sepeti yönetimi API uç noktaları',
        
        'orders' => 'Siparişler',
        'orders_description' => 'Sipariş yönetimi ve takip API uç noktaları',
        
        'addresses' => 'Adresler',
        'addresses_description' => 'Kullanıcı adres yönetimi API uç noktaları',
        
        'currencies' => 'Para Birimleri',
        'currencies_description' => 'Para birimi ve döviz kuru API uç noktaları',
        
        'campaigns' => 'Kampanyalar',
        'campaigns_description' => 'Kampanya ve indirim yönetimi API uç noktaları',
        
        'coupons' => 'Kuponlar',
        'coupons_description' => 'Kupon kodu yönetimi API uç noktaları',
    ],

    // Authentication Endpoints
    'auth' => [
        // Login
        'login_summary' => 'Kullanıcı girişi',
        'login_description' => 'Kullanıcı girişi yapın ve hız sınırlaması ile erişim belirteci alın',
        'login_success' => 'Giriş başarılı',
        'login_failed' => 'Giriş başarısız',
        'login_too_many_attempts' => 'Çok fazla giriş denemesi',
        
        // Register
        'register_summary' => 'Kullanıcı kaydı',
        'register_description' => 'Yeni kullanıcı hesabı oluşturun',
        'register_success' => 'Kayıt başarılı',
        'registration_failed' => 'Kayıt başarısız',
        
        // Logout
        'logout_summary' => 'Kullanıcı çıkışı',
        'logout_description' => 'Mevcut oturumu sonlandırın ve belirteci iptal edin',
        'logout_success' => 'Çıkış başarılı',
        
        // Password Reset
        'forgot_password_summary' => 'Şifre sıfırlama isteği',
        'forgot_password_description' => 'Şifre sıfırlama bağlantısı gönderin',
        'reset_password_summary' => 'Şifre sıfırla',
        'reset_password_description' => 'Yeni şifre ile hesap şifresini sıfırlayın',
        'password_reset_success' => 'Şifre başarıyla sıfırlandı',
        'password_reset_link_sent' => 'Şifre sıfırlama bağlantısı gönderildi',
        
        // Email Verification
        'verify_email_summary' => 'E-posta doğrulama',
        'verify_email_description' => 'E-posta adresini doğrulayın',
        'resend_verification_summary' => 'Doğrulama e-postasını tekrar gönder',
        'verification_sent' => 'Doğrulama e-postası gönderildi',
        'email_verified' => 'E-posta başarıyla doğrulandı',
    ],

    // User Management Endpoints
    'users' => [
        'profile_summary' => 'Kullanıcı profilini al',
        'profile_description' => 'Kimliği doğrulanmış kullanıcının profil bilgilerini alın',
        'profile_retrieved' => 'Profil başarıyla alındı',
        
        'update_profile_summary' => 'Kullanıcı profilini güncelle',
        'update_profile_description' => 'Kimliği doğrulanmış kullanıcının profil bilgilerini güncelleyin',
        'profile_updated' => 'Profil başarıyla güncellendi',
        
        'change_password_summary' => 'Kullanıcı şifresini değiştir',
        'change_password_description' => 'Kimliği doğrulanmış kullanıcının şifresini değiştirin',
        'password_changed' => 'Şifre başarıyla değiştirildi',
        
        'upload_avatar_summary' => 'Kullanıcı avatarı yükle',
        'upload_avatar_description' => 'Kimliği doğrulanmış kullanıcı için yeni bir profil resmi yükleyin',
        'avatar_uploaded' => 'Avatar başarıyla yüklendi',
        
        'delete_avatar_summary' => 'Kullanıcı avatarını sil',
        'delete_avatar_description' => 'Kimliği doğrulanmış kullanıcının profil resmini silin',
        'avatar_deleted' => 'Avatar başarıyla silindi',
        
        'dealer_status_summary' => 'Bayi başvuru durumunu al',
        'dealer_status_description' => 'Kimliği doğrulanmış kullanıcının bayi başvurusunun mevcut durumunu alın',
        'dealer_status_retrieved' => 'Bayi durumu başarıyla alındı',
        
        'dealer_application_summary' => 'Bayi başvurusunda bulunun',
        'dealer_application_description' => 'B2B bayisi olmak için başvuruda bulunun',
        'dealer_application_submitted' => 'Bayi başvurusu başarıyla gönderildi. 3-5 iş günü içinde başvurunuz değerlendirilecektir.',
        'dealer_application_exists' => 'Zaten bir bayi başvurunuz bulunmaktadır',
    ],

    // Wishlist Endpoints
    'wishlist' => [
        'get_wishlist_summary' => 'Kullanıcının istek listesini al',
        'get_wishlist_description' => 'Kimliği doğrulanmış kullanıcının istek listesindeki tüm öğeleri alın',
        'wishlist_retrieved' => 'İstek listesi başarıyla alındı',
        
        'add_to_wishlist_summary' => 'İstek listesine öğe ekle',
        'add_to_wishlist_description' => 'Kullanıcının istek listesine bir ürün veya ürün çeşidi ekleyin',
        'item_added_to_wishlist' => 'Öğe istek listesine başarıyla eklendi',
        
        'get_wishlist_item_summary' => 'Belirli istek listesi öğesini al',
        'get_wishlist_item_description' => 'Kullanıcının istek listesinden belirli bir öğeyi alın',
        'wishlist_item_retrieved' => 'İstek listesi öğesi başarıyla alındı',
        
        'update_wishlist_item_summary' => 'İstek listesi öğesini güncelle',
        'update_wishlist_item_description' => 'Kullanıcının istek listesindeki belirli bir öğeyi güncelleyin',
        'wishlist_item_updated' => 'İstek listesi öğesi başarıyla güncellendi',
        
        'remove_from_wishlist_summary' => 'Öğeyi istek listesinden kaldır',
        'remove_from_wishlist_description' => 'Kullanıcının istek listesinden belirli bir öğeyi kaldırın',
        'item_removed_from_wishlist' => 'Öğe istek listesinden başarıyla kaldırıldı',
        
        'toggle_favorite_summary' => 'Favori durumunu değiştir',
        'toggle_favorite_description' => 'Bir istek listesi öğesinin favori durumunu değiştirin',
        'favorite_status_updated' => 'Favori durumu başarıyla güncellendi',
        
        'clear_wishlist_summary' => 'Tüm istek listesini temizle',
        'clear_wishlist_description' => 'Kullanıcının istek listesindeki tüm öğeleri kaldırın',
        'wishlist_cleared' => 'İstek listesi başarıyla temizlendi',
        
        'wishlist_stats_summary' => 'İstek listesi istatistiklerini al',
        'wishlist_stats_description' => 'Kullanıcının istek listesi hakkında istatistikleri alın',
        'wishlist_stats_retrieved' => 'İstek listesi istatistikleri başarıyla alındı',
    ],

    // Product Endpoints
    'products' => [
        'get_products_summary' => 'Filtreleme ve arama ile ürün listesini al',
        'get_products_description' => 'Gelişmiş filtreleme seçenekleriyle sayfalanmış ürün listesini döndürür',
        'products_retrieved' => 'Ürünler başarıyla alındı',
        
        'get_product_summary' => 'Belirli bir ürünün detaylarını al',
        'get_product_description' => 'Çeşitleri, fiyatlandırması ve stok bilgileriyle birlikte ürün detaylarını alın',
        'product_retrieved' => 'Ürün başarıyla alındı',
        'product_not_found' => 'Ürün bulunamadı',
        
        'get_featured_products_summary' => 'Öne çıkan ürünleri al',
        'get_featured_products_description' => 'Ana sayfada gösterilmek üzere öne çıkan ürünleri alın',
        'featured_products_retrieved' => 'Öne çıkan ürünler başarıyla alındı',
        
        'get_related_products_summary' => 'İlgili ürünleri al',
        'get_related_products_description' => 'Belirli bir ürünle ilgili ürünleri alın',
        'related_products_retrieved' => 'İlgili ürünler başarıyla alındı',
    ],

    // Category Endpoints
    'categories' => [
        'get_categories_summary' => 'Kategori listesini al',
        'get_categories_description' => 'Hiyerarşik kategori yapısını döndürür',
        'categories_retrieved' => 'Kategoriler başarıyla alındı',
        
        'get_category_summary' => 'Belirli bir kategoriyi al',
        'get_category_description' => 'Alt kategorileri ile birlikte kategori detaylarını alın',
        'category_retrieved' => 'Kategori başarıyla alındı',
        'category_not_found' => 'Kategori bulunamadı',
    ],

    // Common Parameters
    'parameters' => [
        'search_term' => 'Arama terimi',
        'category_id_filter' => 'Kategori ID\'sine göre filtrele',
        'min_price' => 'Minimum fiyat filtresi',
        'max_price' => 'Maksimum fiyat filtresi',
        'sort_by' => 'Sıralama kriteri',
        'sort_direction' => 'Sıralama yönü (asc/desc)',
        'page' => 'Sayfa numarası',
        'per_page' => 'Sayfa başına öğe sayısı',
        'priority_filter' => 'Öncelik filtresi (1=Düşük, 2=Orta, 3=Yüksek, 4=Acil)',
        'favorites_only' => 'Sadece favori öğeleri göster',
        'available_only' => 'Sadece stokta olan öğeleri göster',
        'currency_code' => 'Para birimi kodu (TRY, USD, EUR)',
        'include_inactive' => 'Pasif ürünleri dahil et',
    ],

    // Common Responses
    'responses' => [
        'success' => 'Başarılı',
        'created' => 'Oluşturuldu',
        'updated' => 'Güncellendi',
        'deleted' => 'Silindi',
        'not_found' => 'Bulunamadı',
        'unauthorized' => 'Yetkisiz erişim',
        'forbidden' => 'Yasak',
        'validation_error' => 'Doğrulama hatası',
        'server_error' => 'Sunucu hatası',
        'too_many_requests' => 'Çok fazla istek',
    ],

    // Field Descriptions
    'fields' => [
        'email' => 'E-posta adresi',
        'password' => 'Şifre',
        'password_confirmation' => 'Şifre tekrarı',
        'current_password' => 'Mevcut şifre',
        'device_name' => 'Cihaz adı',
        'name' => 'Ad soyad',
        'first_name' => 'Ad',
        'last_name' => 'Soyad',
        'phone' => 'Telefon numarası',
        'date_of_birth' => 'Doğum tarihi',
        'gender' => 'Cinsiyet',
        'company_name' => 'Şirket adı',
        'tax_number' => 'Vergi numarası',
        'business_type' => 'İşletme türü',
        'annual_volume' => 'Beklenen yıllık satın alma hacmi',
        'business_address' => 'İşletme adresi',
        'website' => 'Web sitesi',
        'reference_customers' => 'Referans müşteriler',
        'additional_notes' => 'Ek notlar',
        'avatar' => 'Profil resmi dosyası (maksimum 2MB, formatlar: jpg, jpeg, png, gif)',
        'product_id' => 'Ürün ID\'si',
        'product_variant_id' => 'Ürün çeşidi ID\'si',
        'notes' => 'Bu ürün hakkında notlar',
        'priority' => 'Öncelik seviyesi',
        'is_favorite' => 'Favori olarak işaretle',
        'notification_preferences' => 'Bildirim tercihleri',
        'email_notifications' => 'E-posta bildirimleri',
        'sms_notifications' => 'SMS bildirimleri',
        'marketing_emails' => 'Pazarlama e-postaları',
    ],

    // Example Values
    'examples' => [
        'email' => 'kullanici@example.com',
        'password' => 'guvenli123',
        'device_name' => 'mobile_app',
        'name' => 'Ahmet Yılmaz',
        'first_name' => 'Ahmet',
        'last_name' => 'Yılmaz',
        'phone' => '+90 555 123 4567',
        'company_name' => 'ABC Güvenlik Ekipmanları Ltd.',
        'tax_number' => '1234567890',
        'business_type' => 'Güvenlik Ekipmanları Perakendecisi',
        'annual_volume' => 50000.00,
        'website' => 'https://abcguvenlik.com',
        'search_term' => 'güvenlik ayakkabısı',
        'product_notes' => 'Ofis için gerekli',
    ],

    // Error Messages
    'errors' => [
        'item_already_in_wishlist' => 'Bu öğe zaten istek listenizde bulunuyor',
        'wishlist_item_not_found' => 'İstek listesi öğesi bulunamadı',
        'current_password_incorrect' => 'Mevcut şifre hatalı',
        'no_avatar_found' => 'Silinecek avatar bulunamadı',
        'dealer_application_exists' => 'Zaten bir bayi başvurunuz bulunmaktadır',
        'variant_product_mismatch' => 'Ürün çeşidi belirtilen ürüne ait değil',
        'invalid_credentials' => 'Geçersiz kimlik bilgileri',
        'account_not_verified' => 'Hesap doğrulanmamış',
        'account_disabled' => 'Hesap devre dışı',
        'token_expired' => 'Belirteç süresi dolmuş',
        'invalid_token' => 'Geçersiz belirteç',
    ],
];