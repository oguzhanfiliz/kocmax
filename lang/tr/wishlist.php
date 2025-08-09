<?php

return [
    // Wishlist Model & Resource Labels
    'wishlist' => 'İstek Listesi',
    'wishlists' => 'İstek Listeleri',
    'wishlist_item' => 'İstek Listesi Öğesi',
    'wishlist_items' => 'İstek Listesi Öğeleri',
    'my_wishlist' => 'İstek Listem',
    'user_wishlist' => 'Kullanıcı İstek Listesi',

    // Basic Actions
    'add_to_wishlist' => 'İstek Listesine Ekle',
    'remove_from_wishlist' => 'İstek Listesinden Kaldır',
    'view_wishlist' => 'İstek Listesini Görüntüle',
    'edit_wishlist_item' => 'İstek Listesi Öğesini Düzenle',
    'update_wishlist_item' => 'İstek Listesi Öğesini Güncelle',
    'delete_wishlist_item' => 'İstek Listesi Öğesini Sil',
    'clear_wishlist' => 'İstek Listesini Temizle',
    'empty_wishlist' => 'İstek Listesini Boşalt',

    // Wishlist Item Properties
    'product' => 'Ürün',
    'product_variant' => 'Ürün Çeşidi',
    'notes' => 'Notlar',
    'priority' => 'Öncelik',
    'is_favorite' => 'Favori',
    'favorite' => 'Favori',
    'added_at' => 'Eklenme Tarihi',
    'added_date' => 'Eklenme Tarihi',
    'created_at' => 'Oluşturulma Tarihi',
    'updated_at' => 'Güncellenme Tarihi',

    // Priority Levels
    'priority_low' => 'Düşük',
    'priority_medium' => 'Orta',
    'priority_high' => 'Yüksek',
    'priority_urgent' => 'Acil',
    'priority_1' => 'Düşük',
    'priority_2' => 'Orta', 
    'priority_3' => 'Yüksek',
    'priority_4' => 'Acil',

    // Filtering & Sorting
    'filter_by_priority' => 'Önceliğe Göre Filtrele',
    'filter_favorites_only' => 'Sadece Favoriler',
    'filter_available_only' => 'Sadece Stokta Olanlar',
    'sort_by_priority' => 'Önceliğe Göre Sırala',
    'sort_by_date' => 'Tarihe Göre Sırala',
    'sort_by_favorite' => 'Favorilere Göre Sırala',

    // Wishlist Statistics
    'statistics' => 'İstatistikler',
    'wishlist_stats' => 'İstek Listesi İstatistikleri',
    'total_items' => 'Toplam Öğe',
    'favorite_items' => 'Favori Öğeler',
    'high_priority_items' => 'Yüksek Öncelikli Öğeler',
    'available_items' => 'Stokta Olan Öğeler',
    'priority_breakdown' => 'Öncelik Dağılımı',
    'total_value' => 'Toplam Değer',
    'oldest_item_date' => 'En Eski Öğe Tarihi',
    'newest_item_date' => 'En Yeni Öğe Tarihi',

    // Priority Breakdown Labels
    'low_priority_count' => 'Düşük Öncelikli',
    'medium_priority_count' => 'Orta Öncelikli',
    'high_priority_count' => 'Yüksek Öncelikli',
    'urgent_priority_count' => 'Acil Öncelikli',

    // Favorite Management
    'toggle_favorite' => 'Favori Durumunu Değiştir',
    'mark_as_favorite' => 'Favori Olarak İşaretle',
    'unmark_as_favorite' => 'Favori İşaretini Kaldır',
    'add_to_favorites' => 'Favorilere Ekle',
    'remove_from_favorites' => 'Favorilerden Kaldır',

    // Empty States
    'empty_wishlist_title' => 'İstek listeniz boş',
    'empty_wishlist_message' => 'Beğendiğiniz ürünleri istek listenize ekleyerek daha sonra kolayca erişebilirsiniz.',
    'no_items_found' => 'Hiç öğe bulunamadı',
    'no_favorite_items' => 'Favori öğeniz bulunmuyor',
    'no_high_priority_items' => 'Yüksek öncelikli öğeniz bulunmuyor',

    // Actions & Buttons
    'continue_shopping' => 'Alışverişe Devam Et',
    'move_to_cart' => 'Sepete Taşı',
    'buy_now' => 'Hemen Satın Al',
    'share_wishlist' => 'İstek Listesini Paylaş',
    'save_for_later' => 'Daha Sonra İçin Kaydet',

    // Success Messages
    'item_added_successfully' => 'Ürün istek listesine başarıyla eklendi',
    'item_removed_successfully' => 'Ürün istek listesinden başarıyla kaldırıldı',
    'item_updated_successfully' => 'İstek listesi öğesi başarıyla güncellendi',
    'favorite_status_updated' => 'Favori durumu başarıyla güncellendi',
    'wishlist_cleared_successfully' => 'İstek listesi başarıyla temizlendi',
    'wishlist_retrieved_successfully' => 'İstek listesi başarıyla alındı',
    'wishlist_item_retrieved_successfully' => 'İstek listesi öğesi başarıyla alındı',
    'wishlist_stats_retrieved_successfully' => 'İstek listesi istatistikleri başarıyla alındı',

    // Error Messages
    'item_already_in_wishlist' => 'Bu ürün zaten istek listenizde bulunuyor',
    'item_not_found' => 'İstek listesi öğesi bulunamadı',
    'wishlist_item_not_found' => 'İstek listesi öğesi bulunamadı',
    'product_not_found' => 'Ürün bulunamadı',
    'product_variant_not_found' => 'Ürün çeşidi bulunamadı',
    'variant_product_mismatch' => 'Ürün çeşidi belirtilen ürüne ait değil',
    'unauthorized_access' => 'Bu işlem için yetkiniz bulunmuyor',
    'wishlist_access_denied' => 'Bu istek listesine erişim izniniz yok',

    // Validation Messages
    'product_required' => 'Ürün seçimi zorunludur',
    'invalid_priority' => 'Geçersiz öncelik değeri',
    'notes_too_long' => 'Notlar çok uzun (maksimum 1000 karakter)',
    'invalid_product_variant' => 'Geçersiz ürün çeşidi',

    // Notifications
    'item_added_to_wishlist' => ':product istek listesine eklendi',
    'item_removed_from_wishlist' => ':product istek listesinden kaldırıldı',
    'wishlist_item_updated' => 'İstek listesi öğesi güncellendi',
    'wishlist_cleared' => ':count öğe istek listesinden kaldırıldı',

    // Help Text & Descriptions
    'priority_help' => 'Bu öğenin sizin için ne kadar önemli olduğunu belirtir (1=Düşük, 2=Orta, 3=Yüksek, 4=Acil)',
    'notes_help' => 'Bu ürün hakkında özel notlarınız (isteğe bağlı)',
    'favorite_help' => 'Bu öğeyi favori olarak işaretleyerek daha kolay erişebilirsiniz',
    'availability_help' => 'Sadece stokta bulunan ürünleri gösterir',
    'wishlist_description' => 'Beğendiğiniz ürünleri istek listenize ekleyerek daha sonra kolayca erişebilir ve takip edebilirsiniz.',

    // Meta Information
    'removed_count' => 'Kaldırılan Öğe Sayısı',
    'meta_information' => 'Genel Bilgiler',
    'last_updated' => 'Son Güncellenme',
    'item_count' => 'Öğe Sayısı',

    // Responsive Labels (for mobile)
    'mobile_add_to_wishlist' => '♡',
    'mobile_remove_from_wishlist' => '♥',
    'mobile_favorite' => '★',
    'mobile_priority_high' => '!',
    'mobile_priority_urgent' => '!!',
];