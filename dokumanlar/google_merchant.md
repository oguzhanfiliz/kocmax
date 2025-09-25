Ürün verileri spesifikasyonu
Bu kılavuzun amacı, ürün bilgilerinizi Merchant Center için biçimlendirirken size yardımcı olmaktır. Google, bu verileri kullanarak verilerin doğru sorgularla eşleştirilmesini sağlar. Ürün verilerinizi doğru biçimde paylaşmak, ürünleriniz için başarılı reklamlar ve ücretsiz listelemeler oluşturmak açısından önemlidir.

Bu makalede ele alınan konular:
Başlamadan önce
Tanımlar
Ürün verisi özellikleri:
Temel ürün verileri
Fiyat ve stok durumu
Ürün kategorisi
Ürün tanımlayıcıları
Detaylı ürün açıklaması
Alışveriş kampanyaları ve diğer yapılandırmalar
Pazar yerleri
Hedefler
Kargo
Başlamadan önce
Diğer koşullar
Ürün verilerinizi biçimlendirme
Tanımlar
Ürün: Potansiyel müşterilerin Google'da aradığı üründür.
Öğe: Metin feed'i, XML feed'i veya API olarak ürün verilerinize eklenmiş bir üründür. Örneğin, metin feed'inizdeki bir satır, öğe olarak kabul edilir.
Varyant: Bir ürünün farklı çeşitlerine verilen addır. Örneğin, farklı bedenleri olan bir gömleğin beden varyantları vardır.
Required Zorunlu: Bu özelliği göndermeniz gerekir. Göndermediğiniz takdirde ürününüz reklamlarda ve ücretsiz listelemelerde yayınlanamaz.

This icon represents whether the sourced content is dependent where the product attribute is used Duruma göre değişir: Ürüne veya ürününüzün gösterildiği ülkelere göre bu özelliği göndermeniz gerekebilir ya da gerekmeyebilir.

Optional İsteğe bağlı: Ürününüzün performansının artmasına yardımcı olmak istiyorsanız bu özelliği gönderebilirsiniz.

Temel ürün verileri
Bu özellikleri kullanarak gönderdiğiniz ürün bilgileri, ürün reklamları ve ücretsiz listelemelerinizin başarısı için çok önemlidir. Gönderdiğiniz her şeyin bir müşteriye göstermek isteyeceğiniz kalitede olduğundan emin olun.

Özellik ve biçim

Minimum koşullar hakkında

No [id]

Ürününüzün benzersiz tanımlayıcısı

Required Zorunlu

Örnek
A2B4

Söz dizimi
En fazla 50 karakter

Schema.org mülkü: Evet (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Her ürün için benzersiz bir değer kullanın.
Mümkünse ürünün SKU'sunu kullanın.
Verilerinizi güncellerken no değerini aynı tutun.
Yalnızca geçerli unicode karakterleri kullanın.
Ülkeler veya diller genelinde aynı ürün için aynı no değerini kullanın.
Başlık [title]

veya

Yapılandırılmış başlık [structured_title]

Ürününüzün adı

Required Zorunlu

Örnek (Başlık [title]):
Erkek Dokuma Tişört

Örnek (Yapılandırılmış başlık [structured_title]): trained_algorithmic_media:"Adım Adım Keşif: Orijinal Google Mavi ve Turuncu Erkek Ayakkabısı (42 Numara)"

Söz dizimi
Başlık [title]: Düz metin. En fazla 150 karakter

Yapılandırılmış başlık [structured_title]: 2 alt özellik içerir:

Dijital kaynak türü [digital_source_type] (isteğe bağlı): Bu alt özellik 2 değeri destekler:
Varsayılan [default]: İçerik [content] alt özelliği kullanılarak sağlanan başlığın, üretken yapay zekayla oluşturulmadığını belirtir.
Eğitilmiş algoritmik medya [trained_algorithmic_media]: İçerik [content] alt özelliği kullanılarak sağlanan başlığın, üretken yapay zekayla oluşturulduğunu belirtir.
Herhangi bir değer belirtilmezse varsayılan [default] değer kullanılır.

İçerik [content] (zorunlu): Başlık metni. En fazla 150 karakter.
:

Başlık [title]: Yes (Google Arama Merkezi'nde satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin.

Yapılandırılmış başlık [structured_title]: Hayır

Sattığınız ürünü açık şekilde tanımlamak için başlık [title] ve yapılandırılmış başlık [structured_title] özelliğini kullanın.
Üretken yapay zekayla oluşturulan başlıklar için yapılandırılmış başlık [structured_title] özelliğini, diğer başlıklar için ise başlık [title] özelliğini kullanın.
Ürününüzü doğru şekilde açıklayın ve açılış sayfanızdaki başlıkla eşleştirin.
"Ücretsiz kargo" gibi promosyon metinleri, tamamen büyük harfle yazılmış ifadeler veya dikkat çekme amaçlı yabancı karakterler eklemeyin.
Varyantlar için:

Renk veya beden gibi ayırt edici özellikler ekleyin.
Mobil cihazlar için:

Sözleşmeyle birlikte satılıyorsa "sözleşmeli" ifadesini ekleyin.
ABD için taksitli olarak satılıyorsa "ödeme planıyla" ifadesini ekleyin.
Rusya için:

Kitaplar ve diğer bilgi içerikli ürünler için başlığın başına yaş derecelendirmesini ekleyin.
Açıklama [description]

veya

Yapılandırılmış açıklama [structured_description]

Ürününüzün açıklaması

Required Zorunlu

Örnek (açıklama [description]):
%100 organik pamuktan üretilmiş kırmızı renkli bu klasik erkek tişörtü, dar bir kesime ve sol göğüs kısmına işlenmiş özel marka logosuna sahiptir. Soğuk suyla makinede yıkayın. İthal üründür.

Örnek (yapılandırılmış açıklama [structured_description]):

trained_algorithmic_media:"Google Chromecast'in zahmet gerektirmeyen özellikleriyle TV'nizi dönüştürün. Bu şık cihaz, akıllı şekilde televizyonunuza bağlanarak kablosuz akış ve yansıtma seçenekleri sunar. Entegre HDMI bağlayıcısıyla filmler, TV programları, fotoğraflar ve sunular gibi sevdiğiniz içerikleri doğrudan büyük ekranda yayınlayın."

Söz dizimi
Açıklama [description]: Düz metin. En fazla 5.000 karakter

Yapılandırılmış açıklama [structured_description]: 2 alt özellik içerir:

Dijital kaynak türü [digital_source_type] (isteğe bağlı): Bu alt özellik 2 değeri destekler:
Varsayılan [default]: İçerik [content] alt özelliği kullanılarak sağlanan başlığın, üretken yapay zekayla oluşturulmadığını belirtir.
Eğitilmiş algoritmik medya [trained_algorithmic_media]: İçerik [content] alt özelliği kullanılarak sağlanan başlığın, üretken yapay zekayla oluşturulduğunu belirtir.
Herhangi bir değer belirtilmezse varsayılan [default] değer kullanılır.

İçerik [content] (Zorunlu): Açıklama metni. En fazla 5.000 karakter

:

Açıklama [description]: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Yapılandırılmış açıklama [structured_description]: Hayır

Ürününüzü doğru şekilde tanımlamak ve açılış sayfanızdaki açıklamayla eşleştirmek için açıklama [description] ve yapılandırılmış açıklama [structured_description] özelliklerinden birini kullanın.
Üretken yapay zekayla oluşturulan açıklamalar için yapılandırılmış açıklama [structured_description] özelliğini, diğer açıklamalar için ise açıklama [description] özelliğini kullanın.
"Ücretsiz kargo" gibi promosyon metinleri, tamamen büyük harfle yazılmış ifadeler veya dikkat çekme amaçlı yabancı karakterler eklemeyin.
Yalnızca ürünle ilgili bilgileri ekleyin. Mağazanızın bağlantılarını, satış bilgilerini, rakiplerle ilgili ayrıntıları, başka ürünleri veya aksesuarları eklemeyin.
Açıklamanızı biçimlendirmek için satır sonu, liste veya italik yazı gibi biçimleri kullanın.
Bağlantı [link]

Ürününüzün açılış sayfası

Required Zorunlu

Örnek
http://www.example.com/asp/sp.asp?cat=12&id=1030

Schema.org mülkü: Yes(Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Doğrulanmış alan adınızı kullanın.
http veya https ile başlayın.
RFC 2396 veya RFC 1738 ile uyumlu bir kodlanmış URL kullanın.
Yasal olarak gerekli olmadığı sürece herhangi bir ara sayfaya bağlantı vermeyin.
Resim bağlantısı [image_link]

Ürününüzün ana resminin URL'si

Required Zorunlu

Örnek
http://www.example.com/resim1.jpg

Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Resim URL'si için:

Ürününüzün ana resmine giden bir bağlantı ekleyin.
http veya https ile başlayın.
RFC 2396 veya RFC 1738 ile uyumlu bir kodlanmış URL kullanın.
URL'nizin Google tarafından taranabileceğinden emin olun (Googlebot ve Googlebot-image tarayıcılarına izin veren robots.txt yapılandırması).
Resim için:

Ürünü doğru şekilde gösterin.
Kabul edilen bir biçim kullanın: JPEG (.jpg/.jpeg), WebP (.webp), PNG (.png), animasyonsuz GIF (.gif), BMP (.bmp) ve TIFF (.tif/.tiff).
Bir resmin ölçeğini artırmayın veya küçük resim göndermeyin.
Tanıtım metni, filigran veya kenarlık eklemeyin.
Yer tutucu veya genel bir resim göndermeyin.
Üretken yapay zeka kullanılarak oluşturulan tüm resimler, resmin yapay zeka tarafından oluşturulduğunu belirten meta verileri (ör. IPTC DigitalSourceTypeTrainedAlgorithmicMedia meta veri etiketi) içermelidir. Project Studio gibi üretken yapay zeka araçları kullanılarak oluşturulan resimlerden, IPTC DigitalSourceType gibi yerleştirilmiş meta veri etiketlerini kaldırmayın. Aşağıdaki IPTC NewsCode'lar, resim oluşturulurken kullanılan dijital kaynağın türünü belirtir ve korunmalıdır.

TrainedAlgorithmicMedia: Resim, örneklendirilmiş içerik modeli kullanılarak oluşturulmuştur.
CompositeSynthetic: Resim, sentetik öğelerden oluşmaktadır.
AlgorithmicMedia: Resim, örneklendirilmiş eğitim verilerine dayalı olmayan bir algoritma tarafından oluşturulmuştur (ör. bir yazılım tarafından matematiksel algoritma kullanılarak oluşturulmuş bir görüntü).
Ek resim bağlantısı [additional_image_link]

Ürününüz için ek bir resmin URL'si

Optional İsteğe bağlı

Örnek
http://www.example.com/image1.jpg

Söz dizimi
En fazla 2.000 karakter

Schema.org mülkü: Hayır

Aşağıdaki istisnalarla resim bağlantısı [image_link] özelliğinin koşullarını karşılayın:
Resimde ürün sergilenirken ve kullanılırken gösterilebilir.
Grafikler veya illüstrasyonlar eklenebilir.
Bu özelliği birden fazla kez kullanarak 10 ürün resmi ekleyebilirsiniz.
Üretken yapay zeka kullanılarak oluşturulan tüm resimler, resmin yapay zeka tarafından oluşturulduğunu belirten meta verileri (ör. IPTC DigitalSourceTypeTrainedAlgorithmicMedia meta veri etiketi) içermelidir. Project Studio gibi üretken yapay zeka araçları kullanılarak oluşturulan resimlerden, IPTC DigitalSourceType gibi yerleştirilmiş meta veri etiketlerini kaldırmayın. Aşağıdaki IPTC NewsCode'lar, resim oluşturulurken kullanılan dijital kaynağın türünü belirtir ve korunmalıdır.

TrainedAlgorithmicMedia: Resim, örneklendirilmiş içerik modeli kullanılarak oluşturulmuştur.
CompositeSynthetic: Resim, sentetik öğelerden oluşmaktadır.
AlgorithmicMedia: Resim, örneklendirilmiş eğitim verilerine dayalı olmayan bir algoritma tarafından oluşturulmuştur (ör. bir yazılım tarafından matematiksel algoritma kullanılarak oluşturulmuş bir görüntü).
3D model bağlantısı [virtual_model_link]

Ürününüzün 3D modelini göstermenizi sağlayan ek bağlantı.

Optional İsteğe bağlı (Yalnızca ABD'de kullanılabilir)

Not: Bu özellik yalnızca klasik Merchant Center deneyiminde kullanılabilir.
Örnek
https://www.google.com/products/xyz.glb

Söz dizimi/tür

URL ("http://" veya "https://" ile başlamalıdır)

En fazla 2.000 karakter

3D model kullanın. Dosyanızın boyutu 15 MB'ı aşmamalıdır. Dosyadaki çözünürlük en fazla 2K olabilir (4K desteklenmez).
Ürün verilerinizde geçerli bir URL sağlayın. Bağlantı .gltf veya .glb dosyasına yönlendirmelidir.
3D modelinizi inceleyin. 3D modelinizin düzgün çalışıp çalışmadığını kontrol etmek için doğrulama aracını kullanabilirsiniz.
Mobil bağlantı [mobile_link]

Mobil cihaz ve masaüstü bilgisayar trafiği için farklı bir URL'niz olduğunda ürününüzün mobil cihazlar için optimize edilmiş açılış sayfası

Optional İsteğe bağlı

Örnek
http://www.m.example.com/asp/ sp.asp?cat=12 id=1030

Söz dizimi
En fazla 2.000 alfanümerik karakter

Schema.org mülkü: Hayır

Bağlantı [link] özelliğinin koşullarını karşılayın.
Fiyat ve stok durumu
Bu özellikler, ürünlerinizin fiyat ve stok durumunu tanımlar. Bu bilgiler, reklamlarda ve ücretsiz listelemelerde potansiyel müşterilere gösterilir. Ürünlerinizin fiyatı ve stok durumu sık sık değişiyorsa ürünlerinizi gösterebilmek için bunu bize bildirmeniz gerekir. Ürün bilgilerini güncel tutmayla ilgili ipuçlarına göz atın.

Özellik ve biçim

Minimum koşullar hakkında kısa bilgi

Stok durumu [availability]

Ürününüzün stok durumu

Required Zorunlu

Örnek
in_stock

Desteklenen değerler

Stokta [in_stock]
Stokta yok [out_of_stock]
Ön sipariş [preorder]
İleri tarihli sipariş [backorder]
Schema.org mülkü: Evet [Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin.]

Ürününüzün stok durumunu doğru şekilde gönderip açılış sayfanızdaki ve ödeme sayfalarınızdaki stok durumuyla eşleştirin.
Stok durumu değeri olarak ön sipariş [preorder] veya ileri tarihli sipariş [backorder] değerini gönderirseniz satın alınabileceği tarih [availability_date] özelliğini de gönderin.
Satın alınabileceği tarih [availability_date]

Ön sipariş olarak sipariş edilmiş ürünün teslimata hazır olacağı tarih

Required Ürün stok durumu preorder olarak ayarlanmışsa zorunludur.

Örnek
(UTC+1 için)
2016-02-24T11:07+0100

Söz dizimi

Maks. 25 alfanümerik karakter
ISO 8601
YYYY-MM-DDThh:mm [+hhmm]
YYYY-MM-DDThh:mmZ
Schema.org mülkü: Evet [Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin.]

Ürününüzün stok durumu preorder olarak ayarlanmışsa bu özelliği kullanın. Gelecek yıl içindeki bir değeri girebilirsiniz.
Ürünün satın alınabileceği tarih de açılış sayfasına eklenmeli ve müşterileriniz tarafından açıkça görülmelidir (ör. "6 Mayıs 2023").
Kesin bir tarih sağlanamıyorsa tahmini bir tarih belirleyebilirsiniz (ör. "Mayıs 2023").
Satılan malların maliyeti [cost_of_goods_sold]

Ürününüzün açıklaması

Optional İsteğe bağlı

Belirlediğiniz muhasebe kuralları kapsamında tanımlandığı şekilde belirli bir ürünün satışıyla ilişkili maliyetler. Bu maliyetler malzeme, işçilik, navlun veya diğer genel masrafları içerebilir. Ürünleriniz için SMM'yi göndererek, brüt kârınızın yanı sıra reklamlarınızın ve ücretsiz listelemelerin sağladığı gelir gibi diğer metrikler hakkında da bilgi edinebilirsiniz.

Örnek
23.00 TRY

Söz dizimi

ISO 4217 kodları
Ondalık işareti olarak "," yerine "." kullanın
Sayı
Schema.org mülkü: Hayır

Para birimi ISO 4217 biçiminde olmalıdır. Örneğin, Türk lirası için TRY.
Ondalık işareti, nokta (.) ile belirtilmelidir. Örneğin, 10.00 TRY.
Geçerlilik bitiş tarihi [expiration_date]

Ürününüzün gösterilmesinin durdurulacağı tarih

Optional İsteğe bağlı

Örnek
(UTC+1 için)
2016-07-11T11:07+0100

Söz dizimi

Maks. 25 alfanümerik karakter
ISO 8601
YYYY-MM-DDThh:mm [+hhmm]
YYYY-MM-DDThh:mmZ
Schema.org mülkü: Hayır

Gelecek 30 gün içindeki bir tarihi kullanın.
Birkaç saatlik gecikme yaşanabileceğini unutmayın.
Fiyat [price]

Ürününüzün fiyatı

Required Zorunlu

Örnek:
15.00 TRY

Söz dizimi

Sayı
ISO 4217
Schema.org mülkü: Evet [Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin.]

Ürünün fiyatını ve para birimini doğru şekilde gönderin ve açılış sayfanız ile ödeme aşamasındaki fiyatla eşleştirin.
Açılış sayfanızda ve ödeme sayfalarında, hedef ülkenin para birimi cinsinden fiyatın bulunması kolay bir yerde gösterildiğinden emin olun.
Ürünün, gönderilen fiyata internet üzerinden satın alınabildiğinden emin olun.
Müşterilerin, bir üyelik programına (ücretsiz veya ücretli) kaydolmak zorunda kalmadan ürünü gönderilen fiyata satın alabilmesini sağlayın.
Bağlılık programı [loyalty_program] özelliğinin kullanılabildiği ülkelerde, hem ücretsiz hem de ücretli üyelikler için üyelere özel fiyat gönderirken bağlılık programı [loyalty_program] özelliğindeki fiyat [price] alt özelliğini kullanın. Üyelere özel fiyatları, fiyat [price] veya indirimli fiyat [sale_price] özelliklerini kullanarak göndermeyin.
Fiyatı 0 olarak göndermeyin. (Sadece sözleşmeyle satılan mobil cihazlarda fiyatın 0 olarak gönderilmesine izin verilir.)
Toptan, paket veya çoklu ambalaj halinde satılan ürünler için:
Minimum satın alma miktarının, paketin veya çoklu ambalajın toplam fiyatını gönderin.
ABD ve Kanada için:
Fiyat [price] özelliğine satış vergisi, ürün ve hizmet vergisi (GST), katma değer vergisi (KDV) veya ithalat vergisi gibi herhangi bir vergi eklemeyin.
Diğer tüm ülkeler için:
Fiyata katma değer vergisi (KDV) veya ürün ve hizmet vergisi (GST) ekleyin.
Fiyatla ilgili bilgileri gönderirken kullanabileceğiniz diğer seçenekler için aşağıdaki özellikleri gözden geçirin:
Birim fiyatlandırma ölçüsü [unit_pricing_measure]
Birim fiyatlandırma baz ölçüsü [unit_pricing_base_measure]
İndirimli fiyat [sale_price]
Abonelik ücreti [subscription_cost]
Taksit [installment]
Bağlılık programı [loyalty_program]
İndirimli fiyat [sale_price]

Ürününüzün indirimli fiyatı

Optional İsteğe bağlı

Örnek:
15.00 TRY

Söz dizimi

Sayı
ISO 4217
Schema.org mülkü: Google Arama Merkezi'nde satıcı ürün listeleme deneyimi (indirimli fiyatlandırma) yapılandırılmış verileri hakkında daha fazla bilgi edinin.

Fiyat [price] özelliğinin koşullarını karşılayın.
Bu özelliği (indirimli fiyat), indirimsiz fiyata göre ayarlanmış olan fiyat [price] özelliğine ek olarak gönderin.
Ürününüzün indirimli fiyatını doğru şekilde gönderin ve açılış sayfanız ile ödeme sayfanızda aynı indirimli fiyatı kullanın.
İndirimli fiyat [sale_price] özelliğini, bağlılık fiyatları (Ücretsiz veya ücretli bir bağlılık programına üyelik gerektirir.) ya da promosyon fiyatları göndermek için kullanmayın. Bunun yerine, desteklenen ülkelerde bağlılık programı [loyalty_program] özelliğini kullanın.
İndirimli fiyat geçerlilik tarihi
[sale_price_effective_date]

İndirimli fiyatın geçerli olduğu tarih aralığı.

Optional İsteğe bağlı

Örnek
(UTC+1 için)
2016-02-24T11:07+0100 /
2016-02-29T23:07+0100

Söz dizimi

En fazla 51 alfanümerik karakter
ISO 8601
YYYY-MM-DDThh:mm [+hhmm]
YYYY-MM-DDThh:mmZ
Başlangıç ve bitiş tarihini / ile ayırın
Schema.org mülkü: Hayır

İndirimli fiyat [sale_price] özelliğiyle beraber kullanın.
Bu özelliği (indirimli fiyat geçerlilik tarihi) göndermezseniz indirimli fiyat her zaman geçerli olur.
Başlangıç tarihi için bitiş tarihinden önceki bir tarih seçin.
Birim fiyatlandırma ölçüsü
[unit_pricing_measure]

Ürününüzün satıldığı şekliyle ölçüsü ve boyutu

Optional İsteğe bağlı (Yerel yasa veya yönetmeliklerin gerektirdiği durumlar hariç)

Örnek
1.5 kg

Söz dizimi
Sayısal değer + birim

Desteklenen birimler

Ağırlık: oz, lb, mg, g, kg
İngiliz hacmi (ABD): floz, pt, qt, gal
Metrik hacim: ml, cl, l, cbm
Uzunluk: in, ft, yd, cm, m
Alan: sqft, sqm
Birim başına: ct
Schema.org mülkü: Evet [Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin.]

Ürünün ambalajsız ölçüsü veya boyutunu kullanın.
Pozitif bir sayı kullanın.
Varyantlar için:
Öğe grubu kodu [item_group_id] için aynı değeri, birim fiyatlandırma ölçüsü için de farklı değerleri ekleyin.
Birim fiyatlandırma baz ölçüsü
[unit_pricing_base_measure]

Ürünün fiyatlandırma için baz ölçüsü (ör. 100 ml'ye ayarlanırsa fiyatın 100 ml'lik birimler halinde hesaplandığı anlamına gelir)

Optional İsteğe bağlı (yerel yasa veya yönetmeliklerin gerektirdiği durumlar hariç)

Örnek
100 g

Söz dizimi
Tam sayı + birim

Desteklenen tam sayılar
1, 10, 100, 2, 4, 8

Desteklenen birimler

Ağırlık: oz, lb, mg, g, kg
İngiliz hacmi (ABD): floz, pt, qt, gal
Metrik hacim: ml, cl, l, cbm
Uzunluk: in, ft, yd, cm, m
Alan: sqft, sqm
Birim başına: ct
Ek desteklenen metrik tam sayısı + birim kombinasyonları
75 cl, 750 ml, 50 kg, 1000 kg

Schema.org mülkü: Evet [Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin.]

Birim fiyatlandırma ölçüsü [unit_pricing_measure] özelliğini gönderdiğinizde isteğe bağlıdır.
Bu özellik için aynı ölçü birimini (birim fiyatlandırma ölçüsü) ve birim fiyatlandırma baz ölçüsünü kullanın.
Ürünün birim fiyatını hesaplamak için fiyatın (veya etkinse indirimli fiyatın) kullanıldığını unutmayın. Örneğin fiyat [price] özelliği 3 TRY, birim fiyatlandırma ölçüsü 150 ml ve birim fiyatlandırma baz ölçüsü 100 ml olarak ayarlandığında birim fiyatı 2 TRY/100 ml olur.
Taksit [installment]

Bir taksitli ödeme planının ayrıntıları

Optional İsteğe bağlı

Not:
Görüntülü reklamlar için kullanılamaz.
Araç reklamları için: Yalnızca belirli Avrupa ülkelerinde kullanılabilir.
Alışveriş reklamları ve ücretsiz listelemeler için: Latin Amerika'da tüm ürün kategorileri için, diğer bazı ülkelerde ise yalnızca kablosuz ürün ve hizmetlerin gösterilmesi için kullanılabilir.
Örnek: (199 avro peşinat ve "finans" kredi türünü ifade eder)
6:30 TRY:199 TRY

Söz dizimi
Bu özellik, 4 alt özellik kullanır:

Ay [months] (zorunlu)
Tam sayı, alıcının ödemesi gereken taksit sayısı.
Tutar [amount] (Zorunlu)
ISO 4217, alıcının her ay ödemesi gereken tutar.
Peşinat [downpayment] (İsteğe bağlı, Latin Amerika'da kullanılamaz)
ISO 4217, alıcının peşin olarak yapması gereken tek seferlik ödeme tutarı. Not: Alt özelliği göndermezseniz varsayılan değer 0 veya "peşinat yok" olur.
Kredi türü [credit_type] (İsteğe bağlı). Bu alt özellik, aşağıdaki desteklenen değerleri kullanır:
Finansman [finance]
Kiralama [lease]
Not: Alt özelliği göndermezseniz varsayılan değer finansman [finance] olur. Bu alt özellik yalnızca araç reklamları için geçerlidir.

Schema.org mülkü: Hayır

Açılış sayfanızda görünen taksit seçeneğiyle eşleştirin.
Bağlılık kartını zorunlu kılmayın.
Fiyat [price] özelliğinin tam ön ödeme için belirlenen toplam fiyatı gösterdiğinden emin olun. İsteğe bağlı peşinatla taksitli alternatif ödeme seçeneği belirtmek için taksit [installment] özelliğini kullanın.
Abonelik ücreti [subscription_cost]

Bir kablosuz ürün için iletişim hizmeti sözleşmesi içeren aylık veya yıllık ödeme planı hakkında ayrıntılı bilgi verir.

Optional İsteğe bağlı (Yalnızca belirli ülkelerde kablosuz ürün ve hizmetlerin gösterilmesi için kullanılabilir)

Not: Görüntülü reklamlar için kullanılamaz.
Örnek
month:12:35.00USD

Söz dizimi

Dönem [period] (Zorunlu)
Tek bir abonelik döneminin süresidir. Bu alt özellik, aşağıdaki desteklenen değerleri kullanır:
Ay [month]
Yıl [year]
Dönem uzunluğu [period_length] (Zorunlu)
Tam sayı; alıcının ödeme yapması gereken abonelik dönemi sayısıdır (ay veya yıl şeklinde).
Tutar [amount] (Zorunlu)
ISO 4217'ye göre alıcının aylık olarak ödemesi gereken tutardır. Bu tutarı görüntülerken Google, daha az yer tutması için ücreti en yakın bütün para birimine yuvarlayabilir. Sağlanan değer, sitenizde gösterilen tutarla tam olarak eşleşmelidir.
Schema.org mülkü: Hayır

Ödeme sırasında ödenmesi gereken toplam tutarı fiyat [price] özelliğine ekleyin.
Taksit [installment] özelliğiyle birlikte kullanıldığında, ödeme sırasında ödenmesi gereken toplam tutarı da taksit [installment] özelliğinin peşinat [downpayment] alt özelliğine dahil edin.
Açılış sayfanızda gösterilen iletişim ödeme planını eşleştirin. Plan, açılış sayfasında kolayca bulunabilmelidir.
Bağlılık programı [loyalty_program]

Bağlılık programı [loyalty_program] özelliği; üyelere özel fiyatları, bağlılık puanlarını ve bağlılık programı üyelerine özel kargo seçeneğini ayarlamanıza olanak tanır.

Optional İsteğe bağlı (Yalnızca ABD, Almanya, Avustralya, Birleşik Krallık, Brezilya, Fransa, Japonya, Kanada ve Meksika'da kullanılabilir.)

Örnek
my_loyalty_program:silver:10 USD::10:

Söz dizimi
Bu özellik, 7 alt özellik kullanır:

Program etiketi [program_label] (tek katmanlı satıcılar için isteğe bağlı)
Merchant Center'daki bağlılık programı ayarlarınızda belirtilen bağlılık programı etiketi.
Katman etiketi [tier_label] (tek katmanlı satıcılar için isteğe bağlı)
Merchant Center'daki program ayarlarınızda belirlenen ve her katmanın avantajlarını ayırt etmek için kullanılan katman etiketi.
Fiyat [price] (isteğe bağlı): Program ve katman için üyelere özel fiyat. Bu fiyatlar, programınızın avantajlarını öne çıkarmak için üye olmayan kullanıcılara teklif edilen fiyatlarla birlikte gösterilir. Bu özellik, ücretsiz ve ücretli üyelikler için kullanılmalıdır.
Bağlılık puanları [loyalty_points] (İsteğe bağlı): Üyelerin web sitenizden ürün satın aldığında kazandığı puanlar. Bu değer tam sayı olmalıdır.
Üyelere özel fiyatın geçerlilik tarihi [member_price_effective_date] (isteğe bağlı): Bu alt özellik, satıcıların üyelere özel fiyatlandırma avantajının ne zaman başlayıp ne zaman sona ereceğini belirtmesine olanak tanır.
Kargo etiketi [shipping_label] (isteğe bağlı): Bu alt özellik, satıcıların hangi tekliflerin bağlılık programı üyelerine özel kargoya uygun olduğunu belirtmesine olanak tanır. Bu değer için kendi tanımınızı seçin.
Schema.org mülkü: Evet (Google Arama Merkezi'nde satıcı ürün listeleme deneyimi (üyelere özel fiyatlar) yapılandırılmış verileri hakkında daha fazla bilgi edinin.)

Merchant Center hesabınızda yapılandırılan bağlılık programı etiketi ve katmanlarıyla eşleştirmek için bağlılık programı [loyalty_program] özelliğini gönderin.
Üyelere özel fiyatların web sitenizde üyelerle açıkça paylaşıldığından emin olun. Bu fiyatlar, bağlılık genel bakış sayfasında, özel etkinlik sayfasında veya başka bir iletişim yöntemi üzerinden üyelerle paylaşılabilir.
Üyelere özel fiyatlar; ürün veri kaynağınızda, açılış sayfanızda ve ödeme sayfanızda aynı olmalıdır.
Ücretsiz ve ücretli katmanlar için üyelere özel fiyat bu özellik kullanılarak gönderilmelidir. Fiyat [price] veya indirimli fiyat [sale_price] özelliğini kullanarak üyelere özel fiyat göndermeye izin verilmez.
Bağlılık programınızda yalnızca bir katman varsa program etiketi [program_label] ve katman etiketi [tier_label] özelliklerini göndermeniz gerekmez.

Program etiketi [program_label] ve katman etiketi [tier_label] büyük/küçük harfe duyarlı değildir.
Minimum fiyat [auto_pricing_min_price]

Bir ürünün fiyatının düşürülebileceği en düşük tutar. Google, bu bilgileri indirimli fiyat önerileri, otomatik indirimler veya dinamik promosyonlar gibi özellikler için kullanır.

Optional İsteğe bağlı

Örnek
15.00 TRY

Söz dizimi

Sayı
ISO 4217
Schema.org mülkü: Hayır

Minimum fiyat [auto_pricing_min_price] özelliğini gönderin.
Otomatik indirimler veya dinamik promosyonlar özelliğini kullanıyorsanız ürününüzün fiyatının düşürülebileceği minimum tutarı belirtmek için bu özelliği gönderin.
İndirimli fiyat önerilerini bir minimum fiyatla sınırlandırmak istediğinizde (ör. yerel fiyatlandırma yasalarına uymak veya reklamı yapılan minimum fiyatı belirtmek için) bu özelliği kullanın.
Maksimum perakende fiyatı [maximum_retail_price]

Ürününüzün fiyatıdır.

Optional İsteğe bağlı (Yalnızca Hindistan'da kullanılabilir.)

Örnek
15.00 INR

Söz dizimi

Sayı
ISO 4217
Ürünün maksimum perakende fiyatını ve para birimini doğru şekilde gönderin. Bu fiyat, açılış sayfanızdaki ve ödeme aşamasındaki fiyatla eşleşmelidir.

Açılış sayfanızda hedef ülkenin para birimi cinsinden fiyatın, bulunması kolay bir yerde gösterildiğinden emin olun.

Fiyatı 0 olarak göndermeyin. (Sözleşmeyle satılan mobil cihazlarda fiyatın 0 olarak gönderilmesine izin verilir.)

ABD ve Kanada için:
Fiyata vergi eklemeyin.
Diğer tüm ülkeler için:
Fiyata katma değer vergisi (KDV) veya ürün ve hizmet vergisi (GST) ekleyin.
Ürün kategorisi
Bu özellikleri, Google Ads'deki reklam kampanyalarınızı düzenlemek ve belirli durumlarda Google'ın otomatik ürün kategorizasyonunu geçersiz kılmak için kullanabilirsiniz.

Özellik ve biçim	Minimum koşullar hakkında kısa bilgi
Google ürün kategorisi [google_product_category]

Optional İsteğe bağlı

Ürününüz için Google tarafından tanımlanan ürün kategorisi

Örnek
Giyim ve Aksesuar > Kıyafet > Dış Giyim > Montlar ve Ceketler

veya

371

Söz dizimi
Google ürün sınıflandırmasındaki değer

Sayısal kategori kimliği veya
Kategorinin tam yolu
Desteklenen değerler

Google ürün sınıflandırması

Schema.org mülkü

Hayır

Yalnızca bir kategori ekleyin.
En alakalı kategoriyi ekleyin.
Kategorinin tam yolunu veya sayısal kategori kodunu ekleyin, ancak bunların ikisini aynı anda eklemeyin. Kategori kimliğinin kullanılması önerilir.
Belirli ürünler için belirli bir kategori ekleyin.
Alkollü içecekler yalnızca belirli kategorilere gönderilmelidir.
Sözleşmeyle satılan mobil cihazlar aşağıdaki kategorilerden birine gönderilmelidir:
Elektronik > Haberleşme > Telefon > Cep Telefonları (No: 267)

Tabletler için: Elektronik > Bilgisayarlar > Tablet Bilgisayarlar (No: 4745)

Hediye Kartları, Sanat ve Eğlence > Parti ve Kutlamalar > Hediyelik Eşyalar > Hediye Kartları ve Sertifikaları (No: 53) şeklinde gönderilmelidir.
Ürün türü [product_type]

Optional İsteğe bağlı

Ürününüz için tanımladığınız ürün kategorisi

Örnek
Ana Sayfa > Kadın > Elbiseler > Maxi Elbiseler

Söz dizimi
En fazla 750 alfanümerik karakter

Schema.org mülkü

Hayır

Tam kategoriyi ekleyin. Örneğin, sadece Elbiseler yerine Ana Sayfa > Kadın > Elbiseler > Maksi Elbiseler ifadesini ekleyin.
Google Ads Alışveriş kampanyalarında teklif verme ve raporlama için yalnızca ilk ürün türü değeri kullanılır.
Ürün tanımlayıcıları
Bu özellikler, küresel pazar yerinde sattığınız ürünleri tanımlayan ürün tanımlayıcıları sağlamak için kullanılır ve reklamlarınız ile ücretsiz listelemelerinizin performansını artırmanıza yardımcı olabilir.

Özellik ve biçim	Minimum koşullar hakkında kısa bilgi
Marka [brand]

Ürününüzün marka adı

Required Zorunlu (Film, kitap ve müzik kayıt markaları hariç tüm yeni ürünler için)

Optional İsteğe bağlı (Diğer tüm ürünler için)

Örnek
Google

Söz dizimi
En fazla 70 karakter

Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Ürünün tüketiciler tarafından genellikle tanınan marka adını girin.
Bir ürün için doğru markanın sağlanması, kullanıcılara en iyi deneyimi sunar ve en yüksek performansı beraberinde getirir.
Yalnızca kendi ürettiğiniz veya genel bir marka kategorisine giren ürünlerin markasını, kendi marka adınız olarak belirtin.
Örneğin, özel etiketli ürünler veya kişiye özel mücevherler satıyorsanız markanın adını kendi marka adınız olarak gönderebilirsiniz.
Markası olmayan ürünler (ör. etiketi olmayan klasik bir elbise, genel elektronik aksesuarlar vb.) için bu alanı boş bırakın.
"Yok", "Genel", "Marka yok" veya "Mevcut değil" gibi değerler göndermeyin.
Uyumlu ürünler için:
Uyumlu ürünü imal eden üreticinin GTIN ve marka bilgisini gönderin.
Ürününüzün, Özgün Donanım Üreticisi (OEM) markasının ürünüyle uyumlu veya bunun bir replikası olduğunu belirtmek için OEM markasını girmeyin.
GTIN [gtin]

Ürününüzün Global Ticari Öğe Numarası (GTIN)

Required Zorunlu (Tekliften en iyi performansı alabilmek üzere bilinen bir GTIN'si olan tüm ürünler için)

Optional İsteğe bağlı (Diğer tüm ürünler için [kesinlikle önerilir])

Örnek
3234567890126

Söz dizimi
Maks. 50 sayısal karakter (değer başına maks. 14 - eklenen boşluklar ve tireler göz ardı edilir)

Desteklenen değerler

UPC (Kuzey Amerika'da / GTIN-12)
323456789012 gibi 12 haneli bir sayı
8 haneli UPC-E kodlarının 12 haneli kodlara dönüştürülmesi gerekir
EAN (Avrupa'da / GTIN-13)
13 haneli bir sayı (ör. 3001234567892)
JAN (Japonya'da / GTIN-13)
8 ya da 13 haneli bir sayı (ör. 49123456 veya 4901234567894)
ISBN (kitaplar için)
10 ya da 13 haneli bir sayı (ör. 1455582344 veya 978-1455582341). Sizde her ikisi de varsa yalnızca 13 haneli sayıyı ekleyin. ISBN-10 artık kullanılmamaktadır ve ISBN-13'e dönüştürülmesi gerekir
ITF-14 (çoklu ambalajlar için / GTIN-14)
14-haneli bir sayı (ör. 10856435001702)
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Tire ve boşlukları çıkarın.
Aşağıdaki koşulları içeren resmi GS1 doğrulama kılavuzunda tanımlandığı üzere yalnızca geçerli GTIN'leri gönderin:
Sağlama hanesi var ve doğru
GTIN sınırlı değil (GS1 ön ek aralıkları 02, 04, 2)
GTIN, kupon değildir (GS1 ön ek aralıkları 98 - 99)
Bir ürün için doğru GTIN'nin paylaşılması, kullanıcılara en iyi deneyimi sunar ve en yüksek performansı beraberinde getirir.
GTIN'yi yalnızca doğru olduğundan eminseniz paylaşın. Emin değilseniz bu özelliği paylaşmayın (Örneğin, tahmini veya gerçek olmayan bir değer paylaşmayın). Gönderdiğiniz ürünlerden GTIN değeri hatalı olanlar reddedilir.
Uyumlu ürünler için:
Uyumlu ürünü imal eden üreticinin GTIN ve marka bilgisini gönderin.
Ürününüzün, Özgün Donanım Üreticisi (OEM) markasının ürünüyle uyumlu veya bunun bir replikası olduğunu belirtmek için OEM markasını girmeyin.
Çoklu ambalajlar için:
Çoklu ambalajla alakalı ürün tanımlayıcılarını kullanın.
Paketler için:
Paketteki ana ürünün ürün tanımlayıcılarını kullanın.
Üretici tarafından GTIN atanmış bir ürün için özelleştirme, oyma veya başka bir kişiselleştirme yöntemi sunuyorsanız:
Ürünün özelleştirme içerdiğini Google'a bildirmek için GTIN'yi gönderin ve paket [is_bundle] özelliğini kullanın.
MPN [mpn]

Ürününüzün Üretici Parça Numarası (MPN).

Required Yalnızca ürününüzün üretici tarafından atanan GTIN'si yoksazorunludur.

Optional Diğer tüm ürünler için isteğe bağlıdır

Örnek
GO12345OOGLE

Söz dizimi
En fazla 70 alfanümerik karakter

Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Yalnızca üretici tarafından atanan MPN'leri gönderin.
Mümkün olan en spesifik MPN'yi kullanın.
Örneğin, bir ürünün farklı renklerinin farklı MPN'leri olması gerekir.
Bir ürün için gerektiğinde doğru MPN'nin paylaşılması, kullanıcılara en iyi deneyimi sunar ve en yüksek performansı beraberinde getirir.
Yalnızca doğru olduğundan eminseniz MPN paylaşın. Emin değilseniz bu özelliği paylaşmayın (Örneğin, tahmini veya gerçek olmayan bir değer paylaşmayın).
Gönderdiğiniz ürünlerden MPN değeri hatalı olanlar reddedilir.
Tanımlayıcı var [identifier_exists]

Ürününüzün GTIN, MPN ve marka gibi benzersiz ürün tanımlayıcılarına (UPI'ler) sahip olup olmadığını belirtmek için kullanın.

Optional İsteğe bağlı

Örnek
hayır

Desteklenen değerler

Evet [yes]
Yeni ürüne üretici tarafından ürün tanımlayıcılar atanır
Hayır [no]
Üründe marka, GTIN veya MPN yoktur (Sağ taraftaki koşullara bakın) no değerine ayarlanırsa yine de sahip olduğunuz UPI'leri sağlayın.
Schema.org mülkü: Hayır

Bu özelliği göndermezseniz varsayılan değer yes olur.
Ürününüzün kategori türü, hangi benzersiz ürün tanımlayıcılarının (GTIN, MPN, marka) gerektiğini belirler.
Şu durumlarda tanımlayıcı var özelliğini gönderin ve değeri no olarak ayarlayın:
Ürününüz bir medya öğesiyse ve GTIN yoksa (Not: ISBN ve SBN kodları GTIN olarak kabul edilir)
Ürününüz giyim eşyasıysa ve marka yoksa
Diğer tüm kategorilerde, ürününüzde GTIN veya MPN ile marka kombinasyonu yoksa
Bir ürünün benzersiz ürün tanımlayıcıları varsa bu özelliği "no" değeriyle paylaşmayın. Aksi durumda ürün reddedilebilir.
Detaylı ürün açıklaması
Bu özellikler, küresel pazar yerinde sattığınız ürünleri tanımlayan ürün tanımlayıcıları sağlamak için kullanılır ve reklamlarınız ile ücretsiz listelemelerinizin performansını artırmanıza yardımcı olabilir. 

Özellik ve biçim	Minimum koşullar hakkında kısa bilgi
Durum [condition]

Ürününüzün satış anındaki durumudur.

Required İkinci el veya yenilenmiş ürünler için zorunludur.

Optional Yeni ürünler için isteğe bağlıdır.

Örnek
new

Desteklenen değerler

Yeni [new]
Yepyeni, orijinal, açılmamış paket
Yenilenmiş [refurbished]
Profesyonel olarak yeniden çalışır duruma getirilmiştir, garantisi vardır, orijinal ambalajında olabilir veya olmayabilir
İkinci el [used]
Daha önce kullanılmıştır, orijinal ambalajı açılmıştır veya ambalajı yoktur.
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

 
Yetişkinler için [adult]

Müstehcen içerik barındıran bir ürünü belirtin.

Required Üründe yetişkinlere uygun içerikler barındıran ürünler için zorunludur.

Örnek
yes

Desteklenen değerler

Evet [yes]
Hayır [no]
Schema.org mülkü: Hayır

Bu ürün çıplaklık veya müstehcen içerik barındırıyorsa özelliğin değerini yes olarak ayarlayın. Özelliği göndermezseniz varsayılan değer no olur. Yetişkinlere yönelik içerik politikası hakkında bilgi edinin.
Web siteniz genellikle yetişkinlerden oluşan bir kitleyi hedefliyorsa ve yetişkinlere yönelik olup çıplaklık içeren veya içermeyen içerikler barındırıyorsa bu durumu Merchant Center ayarlarınızda belirtin.
Merchant Center Next'i kullanıyorsanız bu ayarları İşletme ayrıntıları sekmesinde bulabilirsiniz.
Klasik Merchant Center'ı kullanıyorsanız bu ayarları "Araçlar ve Ayarlar" bölümüne gidip "Hesap"ı seçerek bulabilirsiniz.
Çoklu ambalaj [multipack]

Bir satıcı tarafından tanımlanan çoklu ambalajda satılan benzer ürünlerin sayısıdır.

Required Şu ülkelerde çoklu ambalajda satılan ürünler için zorunludur: ABD, Almanya, Avustralya, Birleşik Krallık, Brezilya, Çekya, Fransa, Hollanda, İspanya, İsviçre, İtalya ve Japonya.

Required Çoklu ambalaj oluşturduysanız Google'daki ücretsiz listelemeler için zorunludur.

Optional Diğer tüm ürünler ve hedef ülkeler için isteğe bağlıdır.

Örnek
6

Söz dizimi
Tamsayı

Schema.org mülkü: Hayır

Benzer ürünlerden oluşan özel bir grup tanımladıysanız ve bunları tek satış birimi olarak (ör. paket halinde 6 adet sabun) satıyorsanız bu özelliği gönderin.
Çoklu ambalajınızdaki ürünlerin sayısını gönderin. Bu özelliği göndermezseniz varsayılan değer 0 olur.
Ürünün üreticisi çoklu ambalajı sizin yerinize oluşturduysa bu özelliği göndermeyin.
Paket [is_bundle]

Bir ürünün satıcı tarafından tanımlanan, tek ana ürünün öne çıkarıldığı farklı ürünlerden oluşan bir özel grup olduğunu belirtir.

Required Şu ülkelerdeki paketler için zorunludur: ABD, Almanya, Avustralya, Birleşik Krallık, Brezilya, Çekya, Fransa, Hollanda, İspanya, İsviçre, İtalya ve Japonya.

Required Ana ürün içeren bir paket oluşturduysanız Google'daki ücretsiz listelemeler için zorunludur.

Optional Diğer tüm ürünler ve hedef ülkeler için isteğe bağlıdır.

Örnek
yes

Desteklenen değerler

Evet [yes]
Hayır [no]
Schema.org mülkü: Hayır

Farklı ürünlerden oluşturduğunuz özel bir paketi satıyorsanız ve pakette bir ana ürün bulunuyorsa (ör. çantası ve lensi ile birlikte bir kamera) yes değerini gönderin. Bu özelliği göndermezseniz varsayılan değer no olur.
Net bir ana ürünün olmadığı paketler için (örneğin, peynir ve bisküvi içeren bir hediye sepeti) bu özelliği kullanmayın.
Sertifika [certification]

Ürününüzle ilişkili sertifikalar (enerji verimlilik dereceleri gibi)

AB, EFTA ülkeleri ve Birleşik Krallık'ta kullanılabilir.

Required Alışveriş reklamlarınızda veya ücretsiz listelemelerinizde belirli sertifika bilgilerinin gösterilmesini gerektiren ürünler için (ör. yerel enerji verimliliği etiketleme yönetmelikleri nedeniyle) zorunludur.

Optional Diğer tüm ürünler için isteğe bağlıdır.

Not: Ürününüzü AB EPREL veritabanında bulamıyorsanız kısa sürecek bir geçiş dönemi boyunca bunun yerine enerji verimlilik sınıfı [energy_efficiency_class], minimum enerji verimlilik sınıfı [min_energy_efficiency_class] ve maksimum enerji verimlilik sınıfı [max_energy_efficiency_class] özelliklerini kullanabilirsiniz.
Örnek

EC:EPREL:123456

Söz dizimi

Bu özellik aşağıdaki alt özellikleri kullanır:

Yetkili [certification_authority] Sertifika yetkilisi. Yalnızca "EC" veya "European_Commission" desteklenir.
Ad [certification_name] Sertifikanın adı. Yalnızca "EPREL" desteklenir.
Kod [certification_code] Sertifikanın kodu. Örneğin, https://eprel.ec.europa.eu/screen/product
/dishwashers2019/123456 bağlantısına sahip EPREL sertifikası için 123456 kodu kullanılır.
Schema.org mülkü: Hayır

Bu özelliği sağlamanız gerekip gerekmediğine karar vermek için AB enerji verimliliği tüzüklerine veya yürürlükteki tüm yerel yasalara bakın. Buna, AB enerji etiketlerinin kapsamındaki ürünler dahildir. Örneğin:

Buzdolapları ve dondurucular
Bulaşık Makineleri
Televizyonlar ve diğer harici monitörler
Ev tipi çamaşır makineleri ve kurutmalı çamaşır makineleri
Doğrudan satış işlevine sahip soğutucu aletler
Işık kaynakları
Enerji verimlilik sınıfı [energy_efficiency_class]

Ürününüzün enerji etiketi

AB, EFTA ülkeleri ve Birleşik Krallık'ta kullanılabilir.

Optional İsteğe bağlı (yerel yasa veya yönetmeliklerin gerektirdiği durumlar hariç)

Not: Bu özelliğin desteği sonlandırılıyor. AB enerji verimlilik sınıfını göstermek için sertifika [certification] özelliğini kullanın.
Örnek
A+

Desteklenen değerler

A+++
A++
A+
A
B
C
D
E
F
G
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Yasaların gerektirdiği enerji etiketini ekleyin.
A+ (A+++ ile G arası) gibi bir enerji verimliliği etiketi oluşturmak için minimum enerji verimlilik sınıfı [min_energy_efficiency_class] ve maksimum enerji verimlilik sınıfı [max_energy_efficiency_class] ile birlikte kullanılır.

Minimum enerji verimlilik sınıfı [min_energy_efficiency_class]

Ürününüzün enerji etiketi

AB, EFTA ülkeleri ve Birleşik Krallık'ta kullanılabilir.

Optional İsteğe bağlı (yerel yasa veya yönetmeliklerin gerektirdiği durumlar hariç)

Not: Bu özelliğin desteği sonlandırılıyor. AB enerji verimlilik sınıfını göstermek için sertifika [certification] özelliğini kullanın.
Yalnızca AB ve İsviçre için geçerlidir.

Örnek
A+++

Desteklenen değerler

A+++
A++
A
B
C
D
E
F
G
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Yasaların gerektirdiği enerji etiketini ekleyin.
A+ (A+++ ile D arası) gibi bir enerji verimliliği etiketi oluşturmak için enerji verimlilik sınıfı [energy_efficiency_class] ve maksimum enerji verimlilik sınıfı [max_energy_efficiency_class] ile birlikte kullanılır.

Maksimum enerji verimlilik sınıfı [max_energy_efficiency]

Ürününüzün enerji etiketi

AB, EFTA ülkeleri ve Birleşik Krallık'ta kullanılabilir.

Optional İsteğe bağlı (yerel yasa veya yönetmeliklerin gerektirdiği durumlar hariç)

Not: Bu özelliğin desteği sonlandırılıyor. AB enerji verimlilik sınıfını göstermek için sertifika [certification] özelliğini kullanın.
Yalnızca AB ve İsviçre için geçerlidir.

Örnek
D

Desteklenen değerler

A+++
A++
A
B
C
D
E
F
G
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Yasaların gerektirdiği enerji etiketini ekleyin
A+ (G ile A+++ arası) gibi metin veya grafik içerikli bir enerji verimliliği etiketi oluşturmak için enerji verimlilik sınıfı [energy_efficiency_class] ve minimum enerji verimlilik sınıfı [min_energy_efficiency_class] ile birlikte kullanılır.

Yaş grubu [age_group]

Ürününüzün hedeflediği demografik gruptur.

Required Şu ülkelerdeki kullanıcıları hedefleyen tüm giyim ürünlerinin yanı sıra yaş grubu atanan tüm ürünler için zorunludur: ABD, Almanya, Birleşik Krallık, Brezilya, Fransa ve Japonya.

Required Tüm Giyim ve Aksesuar (kod: 166) ürünlerinin ücretsiz listelemeleri için zorunludur.

Optional Diğer tüm ürünler ve hedef ülkeler için isteğe bağlıdır.

Örnek
3-12 ay

Desteklenen değerler

Yenidoğan [newborn]
0-3 aylık bebekler
3-12 ay [infant]
3-12 aylık bebekler
1-5 yaş [toddler]
1-5 yaş arası çocuklar
Çocuk [kids]
5-13 yaş arası çocuklar
Yetişkin [adult]
Gençler ve daha büyük yaştaki kişiler
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Her ürün için bir değer ekleyin.
Varyantlar için:
Öğe grubu kodu [item_group_id] için aynı değeri, yaş grubu için de farklı değerleri ekleyin.
Renk [color]

Ürününüzün renkleridir.

Required Şu ülkelerdeki kullanıcıları hedefleyen tüm giyim ürünlerinin yanı sıra farklı renklerde sunulan tüm ürünler için zorunludur: ABD, Almanya, Birleşik Krallık, Brezilya, Fransa ve Japonya.

Required Tüm Giyim ve Aksesuar (kod: 166) ürünlerinin ücretsiz listelemeleri için zorunludur.

Optional Diğer tüm ürünler ve hedef ülkeler için isteğe bağlıdır.

Örnek
Siyah

Söz dizimi
Maksimum 100 alfanümerik karakter (renk başına maksimum 40 karakter)

Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

"0", "2" veya "4" gibi bir sayı kullanmayın.
"#fff000" gibi alfanümerik olmayan karakterler kullanmayın.
R gibi yalnızca bir harf kullanmayın (Çince, Japonca veya Korece dilleri için 红 gibi tek karakter kullanabilirsiniz).
"Resme bakın" şeklinde ürüne veya resme atıfta bulunmayın.
Renk adlarını, "KırmızıPembeMavi" gibi tek kelime olacak şekilde birleştirmeyin. Bunun yerine, bu adları "Kırmızı/Pembe/Mavi" örneğinde olduğu gibi / işaretiyle ayırın. "Çok renkli", "çeşitli", "çeşit", "erkek", "kadın" veya "yok" gibi renk olmayan bir değer kullanmayın.
Ürününüzde birden çok renk varsa önce birincil rengi listeleyin.
Varyantlar için:
Öğe grubu kodu [item_group_id] için aynı değeri, renk [color] için de farklı değerleri ekleyin.
Cinsiyet [gender]

Ürününüzün hedeflediği cinsiyettir.

Required Şu ülkelerdeki kullanıcıları hedefleyen tüm giyim ürünlerinin yanı sıra cinsiyete özel tüm ürünler için zorunludur: ABD, Almanya, Birleşik Krallık, Brezilya, Fransa ve Japonya.

Required Tüm Google Giyim ve Aksesuar (kod: 166) ürünlerinin ücretsiz listelemeleri için zorunludur.

Optional Diğer tüm ürünler ve hedef ülkeler için isteğe bağlıdır.

Örnek
Üniseks

Desteklenen değerler

Erkek [male]
Kadın [female]
Üniseks [unisex]
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Ayakkabı Bağcıkları (kod: 1856) gibi cinsiyete bağlı olmayan bazı Giyim ve Aksesuar (kod: 166) kategorileri için bu özellik zorunlu olmasa da özelliğin kullanılması önerilir.
Varyantlar için:
Öğe grubu kodu [item_group_id] için aynı değeri, cinsiyet için de farklı değerleri ekleyin.
Malzeme [material]

Ürününüzün kumaşı veya malzemesidir.

Required Bir varyant grubundaki farklı ürünleri ayırt etme açısından alakalıysa zorunludur.

Optional Diğer tüm ürünler için isteğe bağlıdır.

Örnek
deri

Söz dizimi
En fazla 200 karakter

Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


Tek bir ürün için birden fazla malzeme (varyant değil) belirtmek istiyorsanız önce birincil malzemeyi, ardından ikincil malzemeleri (/ ile ayrılmış en fazla 2 tane) ekleyin.
Örneğin, "PamukPolyesterElastan" yerine "Pamuk/Polyester/Elastan" olarak ekleyin.
Varyantlar için:
Öğe grubu kodu [item_group_id] özelliği için aynı değeri, malzeme özelliği için de farklı değerleri ekleyin.
Desen [pattern]

Ürününüzün deseni veya grafik baskısıdır.

Required Bir varyant grubundaki farklı ürünleri ayırt etme açısından alakalıysa zorunludur.

Optional Diğer tüm ürünler için isteğe bağlıdır.

Örnek
çizgili
puantiyeli
paisley

Söz dizimi
En fazla 100 karakter

Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


Varyantlar için:
Öğe grubu kodu [item_group_id] için aynı değeri, desen özelliği için de farklı değerleri ekleyin.
Beden [size]

Ürününüzün bedeni

Required zorunludur.

Required Giyim ve Aksesuar > Kıyafet (no:1604) veya Giyim ve Aksesuar > Ayakkabılar (no:187) kategorisinde bulunan tüm ürünlerin ücretsiz listelemeleri için zorunludur.

Optional Diğer tüm ürünler ve hedef ülkeler için isteğe bağlıdır.

Örnek
XL

Söz dizimi
En fazla 100 karakter

Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


Varyantlar için:
Öğe grubu kodu [item_group_id] için aynı değeri, beden [size] için de farklı değerleri ekleyin.
Bedenler birden fazla boyut içeriyorsa tek değerde toplayın. Örneğin, "16/34 Uzun" ifadesi 16 inç boyun ölçüsü ve 34 inç kol uzunluğuna sahip "Uzun" bir modeli belirtir.
Öğenizin bedeni herkese veya çoğu kişiye uyuyorsa one_size, OS, one_size fits_all, OSFA, one_size_fits_most veya OSFM kullanabilirsiniz.
Satıcı tarafından tanımlanan çoklu ambalaj ürünleri için çoklu ambalaj miktarını çoklu ambalaj [multipack] özelliğini kullanarak gönderin. Çoklu ambalaj miktarını size özelliğinin altında göndermeyin.
Beden türü [size_type]

Giyim ürününüzün kesimi

Optional İsteğe bağlı (yalnızca giyim ürünleri için kullanılabilir)

Örnek
maternity

Desteklenen değerler

Normal [regular]
Küçük beden [petite]
Hamile [maternity]
Büyük [big]
Uzun [tall]
Büyük beden [plus]
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


En fazla 2 değer gönderin.
Bu özelliği göndermezseniz varsayılan değer regular olur.
Beden ölçüsü sistemi [size_system]

Ürününüzde kullanılan beden ölçüsü sisteminin ülkesi

Optional İsteğe bağlı (yalnızca giyim ürünleri için kullanılabilir)

Örnek
ABD

Desteklenen değerler

ABD
UK
EU
DE
FR
JP
CN
IT
BR
MEX
AU
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


Bu özelliği göndermezseniz varsayılan değer hedef ülkeniz olur.
Öğe grubu kodu [item_group_id]

Farklı versiyonlar (varyantlar) içeren bir ürün grubunun kodudur.

Required Ürün bir varyantsa ABD, Almanya, Birleşik Krallık, Brezilya, Fransa ve Japonya'da zorunludur.

Required Tüm ürün varyantlarının ücretsiz listelemeleri için zorunludur.

Optional Diğer tüm ürünler ve hedef ülkeler için isteğe bağlıdır.

Örnek
AB12345

Söz dizimi
En fazla 50 alfanümerik karakter

Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


 

Her varyant grubu için benzersiz bir değer kullanın. Mümkünse üst SKU'yu kullanın.
Ürün verilerinizi güncellerken değeri aynı tutun.
Yalnızca geçerli unicode karakterleri kullanın.
Aşağıdaki özelliklerden bir veya birkaçına göre farklılık gösteren ürün grupları için öğe grubu kodu kullanın:
Renk [color]
Beden [size]
Desen [pattern]
Malzeme [material]
Yaş grubu [age_group]
Cinsiyet [gender]
Öğe grubundaki her ürün için aynı özellikleri ekleyin. Örneğin bir ürün, boyutu ve rengine göre farklılık gösteriyorsa öğe grubu kimliği [item_group_id] için aynı değeri paylaşan her üründe beden [size] ile renk [color] değerlerini gönderin.
Ürünleriniz yukarıdaki özelliklerde belirtilmeyen tasarım öğelerine göre değişiklik gösteriyorsa öğe grubu kodunu kullanmayın.
Ürün uzunluğu [product_length]

Ürününüzün uzunluğu

Optional İsteğe bağlı

Örnek
20 cm

Söz dizimi
Sayı + birim

Desteklenen değerler
1-3000

Ondalık değerler desteklenir
Desteklenen birimler

cm
in
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


Ürün ölçüm özelliklerinden olabildiğince fazla sayıda ekleyin.
Her ürün boyutu özelliği (ürün uzunluğu, genişliği ve yüksekliği dahil) için aynı ölçü birimini kullanın. Aksi takdirde bilgiler gösterilmez.
Ürün genişliği [product_width]

Ürününüzün genişliği

Optional İsteğe bağlı

Örnek
20 cm

Söz dizimi
Sayı + birim

Desteklenen değerler
1-3000

Ondalık değerler desteklenir
Desteklenen birimler

cm
in
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


Ürün ölçüm özelliklerinden olabildiğince fazla sayıda ekleyin.
Her ürün boyutu özelliği (ürün uzunluğu, genişliği ve yüksekliği dahil) için aynı ölçü birimini kullanın. Aksi takdirde bilgiler gösterilmez.
Ürün yüksekliği [product_height]

Ürününüzün yüksekliği

Optional İsteğe bağlı

Örnek
20 cm

Söz dizimi
Sayı + birim

Desteklenen değerler
1-3000

Ondalık değerler desteklenir
Desteklenen birimler

cm
in
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


Ürün ölçüm özelliklerinden olabildiğince fazla sayıda ekleyin.
Her ürün boyutu özelliği (ürün uzunluğu, genişliği ve yüksekliği dahil) için aynı ölçü birimini kullanın. Aksi takdirde bilgiler gösterilmez.
Ürün ağırlığı [product_weight]

Ürününüzün ağırlığı

Optional İsteğe bağlı

Örnek
3,5 lb

Söz dizimi
Sayı + birim

Desteklenen değerler
0-2000

Ondalık değerler desteklenir
Desteklenen birimler

lb
oz
g
kg
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)


Bu özellik için gerçek, toplam ürün ağırlığını kullanın.
Ürününüz birden fazla parça halinde sunuluyorsa (ör. bir paketin parçası olarak) listelemedeki tüm parçaların toplam ağırlığını kullanın.
Ürün ayrıntıları [product_detail]

Ürününüzün teknik özellikleri veya ürünle ilgili ek bilgiler

Optional İsteğe bağlı

Örnek
Genel:Ürün Türü:Dijital oynatıcı

Söz dizimi
Bu özellik, 3 alt özellik kullanır:

Bölüm adı [section_name]: En fazla 140 karakter
Özellik adı [attribute_name]: En fazla 140 karakter
Özellik değeri [attribute_value]: En fazla 1000 karakter
Schema.org mülkü: Hayır

Diğer özelliklerde yer alan bilgiler, tamamen büyük harfle yazılmış ifadeler, dikkat çekmeyi amaçlayan yabancı karakterler, tanıtım metinleri, anahtar kelimeler veya arama terimleri eklemeyin.
Fiyat, indirimli fiyat, indirim tarihleri, gönderim bedeli, gönderim tarihi, zamanla alakalı diğer bilgiler veya şirketinizin adı gibi bilgileri eklemeyin.
Özellik adını ve değerini, yalnızca değer onaylandığı zaman sağlayın. Örneğin, gıda ürünlerinin vejetaryen olmadığı durumlarda "Vejetaryen=False" değerini girin.
Ürünün öne çıkan özelliği [product_highlight]

Ürünlerinizin öne çıkan özelliklerinden en alakalı olanlar

Optional İsteğe bağlı

Örnek
Netflix, YouTube ve HBO Max gibi binlerce uygulamayı destekler

Söz dizimi
En fazla 150 karakter

Schema.org mülkü: Hayır

Ürünün öne çıkan 2 ila 100 özelliğini kullanın.
Yalnızca ürünü tanıtın.
Anahtar kelimeleri veya arama terimlerini listelemeyin.
Tanıtım metinleri, tamamı büyük harften oluşan metinler veya dikkat çekmeye yönelik yabancı karakterler eklemeyin.
Alışveriş kampanyaları ve diğer yapılandırmalar
Bu özellikler, Google Ads'de reklam kampanyaları oluşturduğunuzda ürün verilerinizin nasıl kullanıldığının kontrol edilmesine olanak tanır.

Özellik ve biçim	Minimum koşullar hakkında kısa bilgi
Ads yönlendirmesi [ads_redirect]

Ürün sayfanız için ek parametreler belirtmek amacıyla kullanılan URL. Müşteriler, bağlantı [link] veya mobil bağlantı [mobile_link] özellikleri için gönderdiğiniz değer yerine bu URL'ye yönlendirilir.

Optional İsteğe bağlı

Örnek
http://www.example.com/product.html

Söz dizimi
En fazla 2.000 karakter

Schema.org mülkü: Hayır

Bağlantı [link] özelliğinde (ve varsa mobil bağlantı [mobile_link] özelliğinde) kullanılanla aynı kayıtlı alanı gönderin.
Geçerli kayıtlı alanlar arasında "example.com", "m-example.com", "example.co.uk", "example.com.ai" ve "bar.tokyo.jp" yer alır.
"example.zz" veya "example.comic" gibi geçersiz alanlarla gönderilen URL'ler kabul edilmez. Geçerli kayıtlı alanlarla ilgili daha fazla ayrıntı için ads yönlendirmesi makalesine göz atın.
Özel etiket 0–4 [custom_label_0-4]

Alışveriş kampanyalarında teklif ve raporların düzenlenmesine yardımcı olmak için bir ürüne atadığınız etikettir.

Optional İsteğe bağlı

Örnek
Seasonal
Clearance
Holiday
Sale
Price range

Söz dizimi
En fazla 100 karakter

Schema.org mülkü: Hayır

Alışveriş kampanyanızda tanıyacağınız bir değer kullanın. Değer, reklamlarınızı ve ücretsiz listelemelerinizi gören müşterilere gösterilmez.
Bu özelliği birden fazla kez ekleyerek ürün başına 5 adede kadar özel etiket gönderebilirsiniz:
custom_label_0
custom_label_1
custom_label_2
custom_label_3
custom_label_4
Merchant Center hesabınızdaki her özel etiket için yalnızca 1.000 benzersiz değer kullanın.
Promosyon kodu [promotion_id]

Ürünleri promosyonlarla eşleştirmenizi sağlayan bir tanımlayıcıdır.

Optional İsteğe bağlı (ABD, Almanya, Avustralya, Birleşik Krallık, Fransa ve Hindistan'daki promosyonlar için zorunludur.)

Örnek
ABC123

Söz dizimi
En fazla 50 karakter

Schema.org mülkü: Hayır

Boşluk veya sembol (ör. %, !) içermeyen benzersiz ve büyük/küçük harfe duyarlı bir kod kullanın.
Belirli promosyonları belirli ürünlerle eşleştirebilmek için ürün ve promosyon verilerinizde aynı promosyon kodunu gönderin.
Bu özelliği birden fazla kez ekleyerek bir ürün için 10 adede kadar promosyon kodu gönderebilirsiniz.
Yaşam tarzı resim bağlantısı [lifestyle_image_link]

Optional İsteğe bağlı

Ürünün yaşam tarzı resminin URL'sini eklemek için kullanılan özelliktir.

Yalnızca gezinme odaklı platformlarda kullanılabilir.

Örnek

https://www.example.com/image1.jpg

Söz dizimi

En fazla 2.000 karakter

Schema.org mülkü: Hayır

Desteklenen dosya biçimindeki bir resme yönlendiren bir URL kullanın.
http veya https ile başlayın ve RFC 3986'ya uyun.
Tüm simgeleri veya boşlukları URL kodlu varlıklarla değiştirin.
Google'ın URL'nizi tarayabileceğinden emin olun.
Üretken yapay zeka kullanılarak oluşturulan tüm resimler, resmin yapay zeka tarafından oluşturulduğunu belirten meta verileri (ör. IPTC DigitalSourceTypeTrainedAlgorithmicMedia meta veri etiketi) içermelidir. Project Studio gibi üretken yapay zeka araçları kullanılarak oluşturulan resimlerden, IPTC DigitalSourceType gibi yerleştirilmiş meta veri etiketlerini kaldırmayın. Aşağıdaki IPTC NewsCode'lar, resim oluşturulurken kullanılan dijital kaynağın türünü belirtir ve korunmalıdır.

TrainedAlgorithmicMedia: Resim, örneklendirilmiş içerik modeli kullanılarak oluşturulmuştur.
CompositeSynthetic: Resim, sentetik öğelerden oluşmaktadır.
AlgorithmicMedia: Resim, örneklendirilmiş eğitim verilerine dayalı olmayan bir algoritma tarafından oluşturulmuştur (ör. bir yazılım tarafından matematiksel algoritma kullanılarak oluşturulmuş bir görüntü).
Pazar yerleri
Pazar yeriyseniz ve çok satıcılı bir hesap kullanıyorsanız bu özellikler, ürün verilerinizin kullanılma şeklinin kontrolüne olanak tanır.

Özellikler ve biçim	Koşullar hakkında kısa bilgi
Harici satıcı kimliği [external_seller_id]

Required Çok satıcılı hesaplar için zorunludur.

Pazar yerleri tarafından satıcıları harici olarak tanımlamak için kullanılır (örneğin, bir web sitesinde).

Örnek

SaticininHerkeseAcikAdi1991

Söz dizimi

1-50 karakter

Schema.org mülkü: Hayır

Her satıcı için benzersiz bir değer kullanın.
Verilerinizi güncellerken kimliği aynı tutun.
Yalnızca geçerli karakterler kullanın. Kontrol, işlev veya gizli alan karakterleri gibi geçersiz karakterlerden kaçının.
Ülkeler veya diller genelinde aynı satıcı için aynı kimliği kullanın.
Hedefler
Bu özellikler, içeriğinizin gösterilebileceği farklı konumları kontrol etmek için kullanılabilir. Örneğin, bir ürünün dinamik yeniden pazarlama kampanyasında gösterilmesini ancak Alışveriş reklamı kampanyasında gösterilmemesini istiyorsanız bu özelliği kullanabilirsiniz.

Özellikler ve biçim	Koşullar hakkında kısa bilgi
Hariç tutulan hedef [excluded_destination]

Bir ürünü belirli bir reklam kampanyası türünden hariç tutmak için kullanabileceğiniz bir ayardır.

Optional İsteğe bağlı

Örnek
Shopping_ads

Desteklenen değerler

Shopping_ads
Buy_on_Google_listings
Display_ads
Local_inventory_ads
Free_listings
Free_local_listings
YouTube_Shopping
Bazı değerler yalnızca Merchant Center'ın klasik sürümünde kullanılabilir.

Schema.org mülkü: Hayır

 
Dahil edilen hedef [included_destination]

Bir ürünü belirli bir reklam kampanyası türüne dahil etmek için kullanabileceğiniz bir ayardır.

Optional İsteğe bağlı

Örnek
Shopping_ads

Desteklenen değerler

Shopping_ads
Buy_on_Google_listings
Display_ads
Local_inventory_ads
Free_listings
Free_local_listings
YouTube_Shopping
Bazı değerler yalnızca Merchant Center'ın klasik sürümünde kullanılabilir.

Schema.org mülkü: Hayır

 
Alışveriş reklamlarından hariç tutulan ülkeler [shopping_ads_excluded_country]

Alışveriş reklamlarında ürünlerinizin reklamının yapıldığı ülkeleri hariç tutabilmenizi sağlayan bir ayar

Optional İsteğe bağlı

Sadece alışveriş reklamları için kullanılabilir

Örnek
DE

Söz dizimi
2 karakter. ISO_3166-1_alpha-2 ülke kodu olmalıdır.

Schema.org mülkü: Hayır

 
Duraklatma [pause]

Bir ürünün tüm reklamlara (Alışveriş reklamları, görüntülü reklamlar ve yerel envanter reklamları dahil) dahil edilmesini duraklatmak ve hızlıca yeniden etkinleştirmek için kullanabileceğiniz bir ayardır. Bir ürün 14 güne kadar duraklatılabilir. 14 günden daha uzun süre duraklatılan ürünler reddedilir. Ürünün yeniden onaylanması için özelliği kaldırın.

Optional İsteğe bağlı

Örnek
reklamlar

Desteklenen değerler
ads

Schema.org mülkü: Hayır

 
 

Kargo
Bu özellikler, doğru gönderim bedeli ve iade maliyetleri sunabilmeniz için hesabınızdaki gönderim ve iade ayarlarıyla birlikte kullanılabilir. İnternetten alışveriş yapan kullanıcılar satın alacakları ürünlere karar vermek için gönderim maliyetlerinin ve hızlarının yanı sıra iade politikalarını da dikkate aldığından kaliteli bilgiler vermeniz önemlidir. 

Özellik ve biçim	Minimum koşullar hakkında kısa bilgi
Gönderim [shipping]

Ürününüzün kargo maliyeti, kargo süreleri ve ürününüzün kargolanacağı konumlar.

This icon represents whether the sourced content is dependent where the product attribute is used Duruma göre değişken

Required Şu ülkelerde Alışveriş reklamları ve ücretsiz listelemeler için kargo maliyetleri zorunludur: ABD, Almanya, Avustralya, Avusturya, Belçika, Birleşik Krallık, Çekya, Fransa, Güney Kore, Hindistan, Hollanda, İrlanda, İspanya, İsrail, İsviçre, İtalya, Japonya, Kanada, Polonya, Romanya, Yeni Zelanda.

Ayrıca, yerel yasalara veya düzenlemelere bağlı olarak da kargo maliyetlerini paylaşmanız gerekebilir.

Optional İsteğe bağlı (ürününüzün kargolandığı ek ülkeleri veya kargo maliyetlerinin zorunlu olmadığı hedefleri belirtmek için)

Desteklenen fiyatlar
0-1.000 USD (Diğer para birimleri için bu sayfaya bakın)

Örnek
US:CA:Overnight:16.00 USD:1:1:2:3

Söz dizimi
Bu özellikte aşağıdaki alt özellikler kullanılır:

Ülke [country] (zorunlu)
ISO 3166 ülke kodu
Bölge [region] (İsteğe bağlı)
Posta kodu [postal_code] (İsteğe bağlı)
Yer kodu [location_id] (İsteğe Bağlı)
Yer grubu adı [location_group_name] (İsteğe bağlı)
Hizmet [service] (İsteğe bağlı)
Hizmet sınıfı veya kargo hızı
Fiyat [price] (İsteğe bağlı)
Sabit kargo maliyeti, gerekirse KDV dahil
Minimum sevkiyata hazırlık süresi [min_handling_time] ve maksimum sevkiyata hazırlık süresi [max_handling_time] (İsteğe bağlı)
Sevkiyata hazırlık süresini belirtmek için
Minimum nakliye süresi [min_transit_time] ve maksimum nakliye süresi [max_transit_time] (İsteğe bağlı)
Nakliye süresini belirtmek için
Schema.org mülkü: Yes (Google Arama Merkezi'nde Satıcı ürün listeleme deneyimi (Ürün, Teklif) yapılandırılmış verileri hakkında daha fazla bilgi edinin)

Ürününüzün kargo maliyetleri Merchant Center hesabınızda tanımlı değilse veya Merchant Center hesabınızda tanımlanan kargo maliyetlerini ya da sürelerini geçersiz kılmanız gerekiyorsa bu ayarı kullanın.
İthalat vergileri, geri dönüşüm ücretleri, telif hakkı ücretleri veya eyalete özgü perakende teslimat ücretleri gibi resmi makamlarca talep edilen ücretleri kargo maliyetine dahil etmeyin.
Satıcı olarak aldığınız ve ürün fiyatına dahil olmayan tüm ek ücretleri dahil edin. Ödeme sırasında, kargoyla doğrudan alakalı olmayan ancak satın alma işlemiyle alakalı olan ücretleri ekleyin. Örneğin, hizmet, işleme, etkinleştirme ve sevkiyata hazırlık ücretleri.
Gönderi etiketi [shipping_label]

Optional İsteğe bağlı

Merchant Center hesap ayarlarında doğru gönderim maliyetlerinin atanmasına yardımcı olmak için bir ürüne atadığınız etiket

Örnek

dayanıksız

Söz dizimi

En fazla 100 karakter

Schema.org mülkü: Hayır

Hesap gönderim ayarlarınızda tanıyacağınız bir değer kullanın. Değer, müşterilere gösterilmez. Örnekler:
Sameday
Büyük boy
Yalnızca FedEx
Gönderi ağırlığı [shipping_weight]

Kargo maliyetinin hesaplanması için kullanılan ürün ağırlığıdır.

Optional İsteğe bağlı (Hesap kargo ayarlarınızdaki kargo şirketi tarafından hesaplanan ücretler için zorunludur.)

Desteklenen ağırlıklar

İngiliz ölçü birimi için 0-2.000 lb
Metrik için 0-1.000 kg
Örnek
3 kg

Söz dizimi
Sayı + birim

Desteklenen birimler

lb
oz
g
kg
Schema.org mülkü: Hayır

Hesap gönderim ayarlarınızı kargo şirketi tarafından hesaplanan ücretlere veya ağırlığa dayalı gönderim hizmetlerine göre belirlediyseniz bu değeri gönderin.
Gönderi uzunluğu [shipping_length]

Kargo maliyetinin boyutsal ağırlığa göre hesaplanması için kullanılan ürün uzunluğudur.

Optional İsteğe bağlı (Hesap kargo ayarlarınızdaki kargo şirketi tarafından hesaplanan ücretler için zorunludur.)

Örnek
20 cm

Söz dizimi
Sayı + birim

Desteklenen değerler

İnç için 1 - 150
Cm için 1 - 400
Desteklenen birimler

in
cm
Schema.org mülkü: Hayır

Hesap gönderim ayarlarınızı kargo şirketi tarafından hesaplanan ücretlere göre belirlediyseniz bu değeri gönderin.
Kargo şirketi tarafından hesaplanan ücretleri kullanırken gönderim boyutu özelliklerini sunmazsanız Google, ücretleri öğenin boyut ağırlığını temel alarak hesaplayamaz. Bu durumda ücretler, kargo ağırlığı [shipping_weight] özelliğinde sunduğunuz değer temel alınarak hesaplanır.
Bu özelliği gönderirseniz kargo boyutuna ilişkin tüm özellikleri gönderin:
Gönderi uzunluğu [shipping_length]
Gönderi genişliği [shipping_width]
Gönderi yüksekliği [shipping_height]
Tek ürün için geçerli olan tüm gönderi boyutu özelliklerinde aynı birimi kullanın.
Google, büyük boyutlu ürünler için ek kargo maliyetini otomatik olarak hesaplamaz. Paketiniz kargo şirketi tarafından büyük veya aşırı büyük olarak değerlendirilirse tek bir ürün için kargo maliyetini belirlemek amacıyla kargo [shipping] özelliğini kullanın.
Gönderi genişliği [shipping_width]

Kargo maliyetinin boyutsal ağırlığa göre hesaplanması için kullanılan ürün genişliğidir.

Optional İsteğe bağlı (Hesap kargo ayarlarınızdaki kargo şirketi tarafından hesaplanan ücretler için zorunludur.)

Örnek
20 cm

Söz dizimi
Sayı + birim

Desteklenen değerler

İnç için 1 - 150
Cm için 1 - 400
Desteklenen birimler

in
cm
Schema.org mülkü: Hayır

Kargo uzunluğu [shipping_length] özelliğinin koşullarını karşılayın.
Gönderi yüksekliği [shipping_height]

Kargo maliyetinin boyut ağırlığına göre hesaplanması için kullanılan ürün yüksekliğidir.

Optional İsteğe bağlı (Hesap kargo ayarlarınızdaki kargo şirketi tarafından hesaplanan ücretler için zorunludur.)

Örnek
20 cm

Söz dizimi
Sayı + birim

Desteklenen değerler

İnç için 1 - 150
Cm için 1 - 400
Desteklenen birimler

in
cm
Schema.org mülkü: Hayır

Kargo uzunluğu [shipping_length] özelliğinin koşullarını karşılayın.
Gönderinin çıkış yaptığı ülke [ships_from_country]

Ürününüzün genellikle hangi ülkeden gönderileceğini belirtmenize olanak tanıyan bir ayardır.

Optional İsteğe bağlı

Örnek
DE

Söz dizimi
2 karakter. ISO_3166-1_alpha-2  ülke kodu olmalıdır.

Schema.org mülkü: Hayır

Yalnızca bu ürünün genellikle hangi ülkeden gönderildiğini belirtin.
Maksimum sevkiyata hazırlık süresi [max_handling_time]

Bir ürünün siparişinin verilmesi ile ürünün gönderilmesi arasındaki en uzun süredir.

Optional İsteğe bağlı

Örnek
3

Söz dizimi
Tam sayı; 0'dan büyük veya 0'a eşit

Schema.org mülkü: Hayır

Bir ürünün varış yerine ulaşma süresini göstermek istiyorsanız bu özelliği gönderin.
İş günü sayısını (Merchant Center'da yapılandırıldığı şekliyle) gönderin.
Aynı gün gönderilmeye hazır ürünler için 0 değerini gönderin.
Bir zaman aralığı belirtmek için maksimum sevkiyata hazırlık süresi [max_handling_time] özelliğini minimum sevkiyata hazırlık süresi [min_handling_time] özelliğiyle birlikte gönderin.
Gönderi etiketi [shipping_label]

Optional İsteğe bağlı

Merchant Center hesap ayarlarında farklı nakliye süreleri atamanıza yardımcı olması için bir ürüne atadığınız etikettir.

Örnek

İzmir'den

Söz dizimi

En fazla 100 karakter

Schema.org mülkü: Hayır

 
Minimum sevkiyata hazırlık süresi [min_handling_time]

Bir ürünün siparişinin verilmesi ile ürünün gönderilmesi arasındaki en kısa süredir.

Optional İsteğe bağlı

Örnek
1

Söz dizimi
Tam sayı; 0'dan büyük veya 0'a eşit

Schema.org mülkü: Hayır

Maksimum sevkiyata hazırlık süresi [max_handling_time] özelliğinin koşullarını karşılayın.
Ücretsiz kargo eşiği [free_shipping_threshold]

Kargonun ücretsiz olması için gereken minimum sipariş tutarıdır.

Optional İsteğe bağlı

Örnek
TR:16.00 TRY

Söz dizimi
Bu özellikte aşağıdaki alt özellikler kullanılır:

Ülke [country] (zorunlu)
ISO 3166 ülke kodu
Fiyat eşiği [price_threshold] (zorunlu): Kargonun ücretsiz olması için gereken minimum sipariş tutarıdır.
Schema.org mülkü: Hayır

Ücretsiz kargo eşiğinin para birimi, teklifin para birimiyle aynı olmalıdır.
Para birimi ISO 4217 biçiminde olmalıdır. Örneğin, Türk lirası için TRY.
Ondalık işareti nokta (.) olmalıdır. Örneğin, 10.00 TRY.
 