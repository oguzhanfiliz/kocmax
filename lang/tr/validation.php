<?php

return [
    'accepted' => ':attribute kabul edilmelidir.',
    'active_url' => ':attribute geçerli bir URL olmalıdır.',
    'after' => ':attribute şundan daha eski bir tarih olmalıdır :date.',
    'after_or_equal' => ':attribute tarihi :date tarihinden sonra veya aynı tarihte olmalıdır.',
    'alpha' => ':attribute sadece harflerden oluşmalıdır.',
    'alpha_dash' => ':attribute sadece harfler, rakamlar ve tirelerden oluşmalıdır.',
    'alpha_num' => ':attribute sadece harfler ve rakamlar içermelidir.',
    'array' => ':attribute dizi olmalıdır.',
    'before' => ':attribute şundan daha önceki bir tarih olmalıdır :date.',
    'before_or_equal' => ':attribute tarihi :date tarihinden önce veya aynı tarihte olmalıdır.',
    'between' => [
        'numeric' => ':attribute :min - :max arasında olmalıdır.',
        'file' => ':attribute :min - :max kilobayt arasında olmalıdır.',
        'string' => ':attribute :min - :max karakter arasında olmalıdır.',
        'array' => ':attribute :min - :max adet öğe arasında olmalıdır.',
    ],
    'boolean' => ':attribute sadece doğru veya yanlış olmalıdır.',
    'confirmed' => ':attribute tekrarı eşleşmiyor.',
    'date' => ':attribute geçerli bir tarih olmalıdır.',
    'date_equals' => ':attribute :date tarihine eşit bir tarih olmalıdır.',
    'date_format' => ':attribute :format biçimi ile eşleşmiyor.',
    'different' => ':attribute ile :other birbirinden farklı olmalıdır.',
    'digits' => ':attribute :digits rakam olmalıdır.',
    'digits_between' => ':attribute :min ile :max arasında rakam olmalıdır.',
    'dimensions' => ':attribute geçersiz resim boyutlarına sahip.',
    'distinct' => ':attribute alanı yinelenen bir değere sahip.',
    'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
    'ends_with' => ':attribute şu değerlerden biriyle bitmelidir: :values.',
    'exists' => 'Seçili :attribute geçersiz.',
    'file' => ':attribute dosya olmalıdır.',
    'filled' => ':attribute alanının doldurulması zorunludur.',
    'gt' => [
        'numeric' => ':attribute :value değerinden büyük olmalıdır.',
        'file' => ':attribute :value kilobayttan büyük olmalıdır.',
        'string' => ':attribute :value karakterden büyük olmalıdır.',
        'array' => ':attribute :value adetten fazla öğeye sahip olmalıdır.',
    ],
    'gte' => [
        'numeric' => ':attribute :value değerinden büyük veya eşit olmalıdır.',
        'file' => ':attribute :value kilobayttan büyük veya eşit olmalıdır.',
        'string' => ':attribute :value karakterden büyük veya eşit olmalıdır.',
        'array' => ':attribute :value öğe veya daha fazlasına sahip olmalıdır.',
    ],
    'image' => ':attribute resim olmalıdır.',
    'in' => 'Seçili :attribute geçersiz.',
    'in_array' => ':attribute alanı :other içinde mevcut değil.',
    'integer' => ':attribute tam sayı olmalıdır.',
    'ip' => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json' => ':attribute geçerli bir JSON dizgisi olmalıdır.',
    'lt' => [
        'numeric' => ':attribute :value değerinden küçük olmalıdır.',
        'file' => ':attribute :value kilobayttan küçük olmalıdır.',
        'string' => ':attribute :value karakterden küçük olmalıdır.',
        'array' => ':attribute :value adetten az öğeye sahip olmalıdır.',
    ],
    'lte' => [
        'numeric' => ':attribute :value değerinden küçük veya eşit olmalıdır.',
        'file' => ':attribute :value kilobayttan küçük veya eşit olmalıdır.',
        'string' => ':attribute :value karakterden küçük veya eşit olmalıdır.',
        'array' => ':attribute :value öğe veya daha azına sahip olmalıdır.',
    ],
    'max' => [
        'numeric' => ':attribute :max değerinden büyük olmamalıdır.',
        'file' => ':attribute :max kilobayttan büyük olmamalıdır.',
        'string' => ':attribute :max karakterden fazla olmamalıdır.',
        'array' => ':attribute :max adetten fazla öğeye sahip olmamalıdır.',
    ],
    'mimes' => ':attribute dosya tipi :values olmalıdır.',
    'mimetypes' => ':attribute dosya tipi :values olmalıdır.',
    'min' => [
        'numeric' => ':attribute en az :min olmalıdır.',
        'file' => ':attribute en az :min kilobayt olmalıdır.',
        'string' => ':attribute en az :min karakter olmalıdır.',
        'array' => ':attribute en az :min öğeye sahip olmalıdır.',
    ],
    'not_in' => 'Seçili :attribute geçersiz.',
    'not_regex' => ':attribute biçimi geçersiz.',
    'numeric' => ':attribute sayı olmalıdır.',
    'password' => 'Parola hatalı.',
    'present' => ':attribute alanı mevcut olmalıdır.',
    'regex' => ':attribute biçimi geçersiz.',
    'required' => ':attribute alanı gereklidir.',
    'required_if' => ':attribute alanı, :other :value olduğunda gereklidir.',
    'required_unless' => ':attribute alanı, :other :values içinde olmadığında gereklidir.',
    'required_with' => ':attribute alanı :values mevcut olduğunda gereklidir.',
    'required_with_all' => ':attribute alanı herhangi bir :values mevcut olduğunda gereklidir.',
    'required_without' => ':attribute alanı :values mevcut olmadığında gereklidir.',
    'required_without_all' => ':attribute alanı :values herhangi biri mevcut olmadığında gereklidir.',
    'same' => ':attribute ve :other eşleşmelidir.',
    'size' => [
        'numeric' => ':attribute :size olmalıdır.',
        'file' => ':attribute :size kilobayt olmalıdır.',
        'string' => ':attribute :size karakter olmalıdır.',
        'array' => ':attribute :size öğeye sahip olmalıdır.',
    ],
    'starts_with' => ':attribute şu değerlerden biriyle başlamalıdır: :values.',
    'string' => ':attribute dizge olmalıdır.',
    'timezone' => ':attribute geçerli bir saat dilimi olmalıdır.',
    'unique' => ':attribute daha önceden kayıt edilmiş.',
    'uploaded' => ':attribute yüklenemedi. Lütfen dosya boyutunu kontrol edin (maksimum 16MB) ve tekrar deneyin.',
    'url' => ':attribute biçimi geçersiz.',
    'uuid' => ':attribute geçerli bir UUID olmalıdır.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'ad',
        'username' => 'kullanıcı adı',
        'email' => 'e-posta adresi',
        'first_name' => 'ad',
        'last_name' => 'soyad',
        'password' => 'parola',
        'password_confirmation' => 'parola tekrar',
        'city' => 'şehir',
        'country' => 'ülke',
        'address' => 'adres',
        'phone' => 'telefon',
        'mobile' => 'mobil',
        'age' => 'yaş',
        'sex' => 'cinsiyet',
        'gender' => 'cinsiyet',
        'day' => 'gün',
        'month' => 'ay',
        'year' => 'yıl',
        'hour' => 'saat',
        'minute' => 'dakika',
        'second' => 'saniye',
        'title' => 'başlık',
        'content' => 'içerik',
        'description' => 'açıklama',
        'excerpt' => 'özet',
        'date' => 'tarih',
        'time' => 'zaman',
        'available' => 'mevcut',
        'size' => 'boyut',
        'file' => 'dosya',
        'image' => 'resim',
        'featured_image' => 'öne çıkan görsel',
        'gallery_images' => 'galeri görselleri',
    ],
]; 