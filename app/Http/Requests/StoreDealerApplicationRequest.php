<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealerApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Kullanıcı bilgileri
            'user_name' => ['required', 'string', 'max:255', 'min:2'],
            'user_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'user_password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_phone' => ['required', 'string', 'max:20', 'min:10', 'regex:/^[\d\s\-\+\(\)]+$/'],
            
            // Firma bilgileri  
            'company_name' => ['required', 'string', 'max:255', 'min:2'],
            'authorized_person_name' => ['required', 'string', 'max:255', 'min:2'],
            'authorized_person_phone' => ['required', 'string', 'max:20', 'min:10', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'tax_number' => ['required', 'string', 'max:20', 'min:10', 'unique:dealer_applications,tax_number'],
            'tax_office' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:1000', 'min:10'],
            'landline_phone' => ['nullable', 'string', 'max:20', 'min:10', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['required', 'email', 'max:255'], // Application için email (user_email ile aynı olacak)
            'business_field' => ['required', 'string', 'max:255', 'min:2'],
            'reference_companies' => ['nullable', 'string', 'max:2000'],
            
            // Belgeler
            'trade_registry_document' => [
                'required', 
                'file', 
                'mimes:pdf,jpeg,jpg,png', 
                'max:5120'
            ],
            'tax_plate_document' => [
                'required', 
                'file', 
                'mimes:pdf,jpeg,jpg,png', 
                'max:5120'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Kullanıcı mesajları
            'user_name.required' => 'Adınız ve soyadınız zorunludur.',
            'user_name.min' => 'Adınız en az 2 karakter olmalıdır.',
            'user_email.required' => 'E-mail adresiniz zorunludur.',
            'user_email.email' => 'Geçerli bir e-mail adresi giriniz.',
            'user_email.unique' => 'Bu e-mail adresi zaten kullanılmaktadır.',
            'user_password.required' => 'Şifre zorunludur.',
            'user_password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'user_password.confirmed' => 'Şifre onayı eşleşmiyor.',
            'user_phone.required' => 'Telefon numaranız zorunludur.',
            'user_phone.regex' => 'Telefon numarası formatı geçersiz.',
            
            // Firma mesajları
            'company_name.required' => 'Firma ünvanı zorunludur.',
            'company_name.min' => 'Firma ünvanı en az 2 karakter olmalıdır.',
            'authorized_person_name.required' => 'Yetkili kişi adı zorunludur.',
            'authorized_person_phone.required' => 'Yetkili kişi telefonu zorunludur.',
            'authorized_person_phone.regex' => 'Telefon numarası formatı geçersiz.',
            'tax_number.required' => 'Vergi numarası zorunludur.',
            'tax_number.unique' => 'Bu vergi numarası ile daha önce başvuru yapılmış.',
            'tax_office.required' => 'Vergi dairesi zorunludur.',
            'address.required' => 'Adres bilgisi zorunludur.',
            'address.min' => 'Adres en az 10 karakter olmalıdır.',
            'email.required' => 'E-mail adresi zorunludur.',
            'email.email' => 'Geçerli bir e-mail adresi giriniz.',
            'business_field.required' => 'Faaliyet alanı zorunludur.',
            'trade_registry_document.required' => 'Ticaret sicil belgesi zorunludur.',
            'trade_registry_document.mimes' => 'Ticaret sicil belgesi PDF, JPEG, JPG veya PNG formatında olmalıdır.',
            'trade_registry_document.max' => 'Ticaret sicil belgesi maksimum 5MB olmalıdır.',
            'tax_plate_document.required' => 'Vergi levhası belgesi zorunludur.',
            'tax_plate_document.mimes' => 'Vergi levhası belgesi PDF, JPEG, JPG veya PNG formatında olmalıdır.',
            'tax_plate_document.max' => 'Vergi levhası belgesi maksimum 5MB olmalıdır.',
            'website.url' => 'Geçerli bir web sitesi adresi giriniz.',
            'landline_phone.regex' => 'Sabit telefon numarası formatı geçersiz.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'firma ünvanı',
            'authorized_person_name' => 'yetkili kişi adı',
            'authorized_person_phone' => 'yetkili kişi telefonu',
            'tax_number' => 'vergi numarası',
            'tax_office' => 'vergi dairesi',
            'address' => 'adres',
            'landline_phone' => 'sabit telefon',
            'website' => 'web sitesi',
            'email' => 'e-mail',
            'business_field' => 'faaliyet alanı',
            'reference_companies' => 'referans firmalar',
            'trade_registry_document' => 'ticaret sicil belgesi',
            'tax_plate_document' => 'vergi levhası belgesi',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $mergeData = [];

        // User telefon numarasından boşlukları temizle
        if ($this->has('user_phone')) {
            $mergeData['user_phone'] = preg_replace('/\s+/', '', $this->user_phone);
        }

        // Yetkili kişi telefon numaralarından boşlukları temizle
        if ($this->has('authorized_person_phone')) {
            $mergeData['authorized_person_phone'] = preg_replace('/\s+/', '', $this->authorized_person_phone);
        }

        if ($this->has('landline_phone')) {
            $mergeData['landline_phone'] = preg_replace('/\s+/', '', $this->landline_phone);
        }

        // Vergi numarasından boşluk ve özel karakterleri temizle
        if ($this->has('tax_number')) {
            $mergeData['tax_number'] = preg_replace('/[^0-9]/', '', $this->tax_number);
        }

        // Application email'i user_email ile aynı yap
        if ($this->has('user_email')) {
            $mergeData['email'] = $this->user_email;
        }

        if (!empty($mergeData)) {
            $this->merge($mergeData);
        }
    }
}
