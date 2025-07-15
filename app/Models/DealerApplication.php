<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Kitlesel olarak atanabilir öznitelikler.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // The ID of the user who submitted the application | Başvuruyu gönderen kullanıcının ID'si
        'company_name', // The name of the applicant's company | Başvuru sahibinin şirket adı
        'tax_number', // The tax number of the applicant's company | Başvuru sahibinin vergi numarası
        'trade_registry_document_path', // The path to the trade registry document | Ticaret sicil belgesinin yolu
        'tax_plate_document_path', // The path to the tax plate document | Vergi levhası belgesinin yolu
        'status', // The status of the application (e.g., pending, approved, rejected) | Başvurunun durumu (örn. beklemede, onaylandı, reddedildi)
    ];

    /**
     * Get the user who submitted the application.
     * Başvuruyu gönderen kullanıcıyı alır.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
