<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use App\Mail\ContactMail;
use App\Mail\ContactAutoReplyMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Store a new contact message
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10|max:2000',
            ], [
                'name.required' => 'İsim alanı zorunludur.',
                'email.required' => 'E-posta alanı zorunludur.',
                'email.email' => 'Geçerli bir e-posta adresi giriniz.',
                'subject.required' => 'Konu alanı zorunludur.',
                'message.required' => 'Mesaj alanı zorunludur.',
                'message.min' => 'Mesaj en az 10 karakter olmalıdır.',
                'message.max' => 'Mesaj en fazla 2000 karakter olabilir.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Contact kaydını oluştur
            $contact = Contact::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'company' => $request->input('company'),
                'subject' => $request->input('subject'),
                'message' => $request->input('message'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Admin'lere bildirim gönder
            $this->sendAdminNotification($contact);

            // Gönderen kişiye otomatik yanıt gönder
            $this->sendAutoReply($contact);

            Log::info('Yeni iletişim mesajı alındı', [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'subject' => $contact->subject,
                'ip' => $contact->ip_address
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.',
                'contact_id' => $contact->id
            ], 201);

        } catch (\Exception $e) {
            Log::error('İletişim mesajı gönderilirken hata oluştu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyiniz.'
            ], 500);
        }
    }

    /**
     * Send notification to admins
     */
    protected function sendAdminNotification(Contact $contact): void
    {
        try {
            // Admin kullanıcıları bul
            $adminUsers = User::role('admin')->get();

            if ($adminUsers->isEmpty()) {
                // Admin yoksa genel e-posta adresine gönder
                $adminEmail = config('mail.admin_email', 'admin@mutfakyapim.net');
                Mail::to($adminEmail)->send(new ContactMail($contact));
                Log::info('İletişim bildirimi genel admin e-postasına gönderildi', [
                    'admin_email' => $adminEmail,
                    'contact_id' => $contact->id
                ]);
            } else {
                // Her admin'e bildirim gönder
                foreach ($adminUsers as $admin) {
                    Mail::to($admin->email)->send(new ContactMail($contact));
                    Log::info('İletişim bildirimi admin kullanıcısına gönderildi', [
                        'admin_email' => $admin->email,
                        'contact_id' => $contact->id
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Admin bildirim maili gönderilirken hata', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send auto-reply to sender
     */
    protected function sendAutoReply(Contact $contact): void
    {
        try {
            Mail::to($contact->email)->send(new ContactAutoReplyMail($contact));
            Log::info('İletişim otomatik yanıt maili gönderildi', [
                'contact_email' => $contact->email,
                'contact_id' => $contact->id
            ]);
        } catch (\Exception $e) {
            Log::error('Otomatik yanıt maili gönderilirken hata', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
