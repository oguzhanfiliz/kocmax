
# Fiyatlandırma Sistemimizin Kalbi: Neden ve Nasıl Çalışıyor? (Herkes İçin)

Merhaba! Bu doküman, e-ticaret sitemizin arkasındaki "akıllı fiyat motorunun" ne işe yaradığını ve neden bu kadar özel olduğunu anlatmak için hazırlandı. Teknik bir dil yerine, günlük hayattan örneklerle bu sistemin dehasını ve amacını açıklayacağız.

## 1. Problem Neydi? Neden Yeni Bir Sisteme İhtiyaç Duyduk?

Hayal edin ki büyük bir süpermarket yönetiyorsunuz. Eski sistemimiz, bu süpermarketin kasalarının dağınık çalışmasına benziyordu:

*   **Her Kasiyer Farklı Hesap Yapıyordu:** Bir kasiyer toplu alım indirimini elle hesaplarken, diğeri bayilere özel iskontoyu aklından yapmaya çalışıyordu.
*   **Kurallar Karışıktı:** "Hafta sonu %10 indirim" ile "1000 TL üzeri 100 TL indirim" aynı anda denk gelince kasiyerin kafası karışıyor, bazen yanlış indirim yapıyordu.
*   **Yeni Kampanya Eziyetti:** "Yılbaşına özel tüm içeceklere %15 indirim" gibi yeni bir kampanya başlatmak, tüm kasiyerleri tek tek eğitmek ve her birinin kuralı doğru uyguladığından emin olmak gibi büyük bir işti.

Kısacası, sistemimiz **karmaşık, hataya açık ve yavaştı.** Büyümemizin önünde bir engeldi.

## 2. Yeni Sistemin Amacı Ne? "Akıllı Kasa" Fikri

Yeni sistemin temel amacı, tüm bu karmaşayı ortadan kaldıran merkezi ve **"akıllı bir kasa"** yaratmaktı. Bu akıllı kasanın tek bir görevi var: Kim olursan ol, ne alırsan al, sana her zaman **doğru ve en avantajlı fiyatı** saniyeler içinde hesaplamak.

Bu "akıllı kasa"yı tasarlarken, onu gelecekteki tüm ihtiyaçlarımıza cevap verebilecek kadar **esnek, güvenilir ve kolay yönetilebilir** yapmak için bazı çok zeki tasarım desenleri kullandık.

## 3. Akıllı Kasanın Sırları: Tasarım Desenleri

İşte bu sistemi bu kadar güçlü yapan ve "yazılım sanatının" devreye girdiği o sihirli yöntemler:

### ✨ Sır #1: "Strateji Defterleri" (Strategy Pattern)

Akıllı kasamız, her müşteri tipini tanır ve ona özel bir "strateji defteri" kullanır.

*   **Analoji:** Kasiyerin önünde üç farklı defter var:
    1.  **Normal Müşteri Defteri (B2C):** "Ürünün etiket fiyatı neyse odur." yazar.
    2.  **Bayi Defteri (B2B):** "Önce bayinin özel iskontosunu uygula, sonra toplu alım indirimi var mı diye bak." yazar.
    3.  **Ziyaretçi Defteri (Guest):** "Sadece etiket fiyatını kullan, indirim yapma." yazar.

*   **Kritik Nokta:** Yeni bir müşteri tipi mi geldi? Mesela "VIP Gold Üyeler". Tek yapmamız gereken kasaya yeni bir "VIP Gold Defteri" eklemek. Mevcut defterleri veya kasiyerin çalışma şeklini hiç değiştirmiyoruz. Bu, sisteme yeni özellikler eklemeyi inanılmaz kolaylaştırır.

### ✨ Sır #2: "İndirim Uzmanları Zinciri" (Chain of Responsibility)

Fiyat hesaplanırken, ürün bir "indirim uzmanları" hattından geçer. Her uzman sadece kendi alanına bakar.

*   **Analoji:** Bir ürün kasadan geçerken sırayla şu uzmanlara uğrar:
    1.  **Toplu Alım Uzmanı:** "Bu üründen 100'den fazla mı alınıyor? Evet. O zaman ben %5 indirimimi yapıyorum." der ve ürünü bir sonraki uzmana gönderir.
    2.  **Sezon İndirimi Uzmanı:** "Bugün Anneler Günü mü? Hayır. O zaman ben bir şey yapmıyorum." der ve ürünü tovább iletir.
    3.  **Kupon Kodu Uzmanı:** "Müşterinin kuponu var mı? Evet. O zaman ben de 15 TL indirim yapıyorum." der.

*   **Kritik Nokta:** Her uzman sadece kendi işinden sorumlu. "Kara Cuma" indirimi mi eklemek istiyoruz? Hattın sonuna bir "Kara Cuma Uzmanı" eklememiz yeterli. Bu yapı, indirim kurallarını birbirinden bağımsız ve yönetilebilir kılar. Kurallar asla birbiriyle karışmaz.

### ✨ Sır #3: "Fiyat Süsleyicileri" (Decorator Pattern)

Tüm indirimler bittikten sonra, fiyat son sunuma hazırlanır.

*   **Analoji:** Elimizde indirimleri yapılmış net bir fiyat var (hediyenin kendisi).
    *   **Vergi Süsleyicisi:** Fiyatı alır, üzerine KDV'yi ekler ve "vergi paketi" yapar.
    *   **Para Birimi Süsleyicisi:** Fiyatı alır ve başına "₺" işareti koyar.
    *   **Kargo Süsleyicisi:** Fiyatı alır ve üzerine kargo ücretini ekler.

*   **Kritik Nokta:** Bu "süsleyiciler" sayesinde, vergi hesaplama veya para birimi gösterme gibi adımları ana fiyat hesaplama mantığından tamamen ayırırız. Her parça kendi işini yapar.

## 4. Yazımda Nelere Dikkat Edildi? (İşin Felsefesi)

Bu sistemi "sağlam" yapmak için bazı temel prensiplere sadık kaldık. Bunu bir bina inşa etmek gibi düşünebilirsiniz:

*   **Her Tuğlanın Tek Bir Görevi Var (Single Responsibility):** Vergi hesaplayan kod, sadece vergi hesaplar. İndirimleri yöneten kod, sadece indirimleri yönetir. Bu, hataları bulmayı ve düzeltmeyi çok kolaylaştırır.
*   **Binayı Yıkmadan Oda Ekleme (Open/Closed Principle):** Sistem, yeni indirim türleri veya yeni müşteri stratejileri eklemeye **"AÇIK"**, ama bu eklemeleri yaparken mevcut çalışan sistemi bozmaya **"KAPALI"**'dır. Yeni bir özellik için temel sistemi asla riske atmayız.
*   **Tüm Parçalar Uyumlu (Liskov Substitution & Interface Segregation):** Kullandığımız tüm "strateji defterleri" veya "indirim uzmanları" aynı dili konuşur. Akıllı kasa, bir uzmanın ne iş yaptığını bilmek zorunda değildir, sadece onun bir "indirim uzmanı" olduğunu bilir. Bu, sistemi inanılmaz esnek yapar.
*   **Patron Detaylarla Uğraşmaz (Dependency Inversion):** Ana fiyat motoru (patron), indirimlerin nasıl hesaplandığının (detaylar) incelikleriyle ilgilenmez. Sadece "İndirimleri hesapla!" der ve "İndirim Uzmanları Zinciri" bu işi halleder. Bu, sistemin parçalarını birbirinden bağımsız hale getirir.

## Özetle: Ne Kazandık?

Bu tasarım sayesinde artık elimizde:

*   **Esnek bir sistem var:** Yarın "Sadece İstanbul'daki müşterilere özel %10 yağmur indirimi" gibi çılgın bir kampanya yapmak istesek bile, bunu sisteme kolayca ekleyebiliriz.
*   **Güvenilir bir yapı var:** Her parça kendi işini yaptığı için hata yapma olasılığı çok düşük. Bir kuralın diğerini ezmesi veya yanlış hesaplama yapılması neredeyse imkansız.
*   **Kolay yönetilebilir bir panel var:** `Kullanım Kılavuzu`'nda gördüğünüz gibi, tüm bu karmaşık yapı, sizin için hazırlanmış basit ve anlaşılır admin paneli formları aracılığıyla yönetiliyor. Artık kod yazmadan, sadece form doldurarak en karmaşık kampanyaları bile oluşturabilirsiniz.

Bu proje, sadece bir fiyatlandırma sistemi değil; aynı zamanda şirketin gelecekteki büyüme ve pazarlama stratejilerinin önünü açan, ölçeklenebilir ve modern bir mühendislik yatırımıdır.
