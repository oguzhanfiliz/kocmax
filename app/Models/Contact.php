<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email', 
        'phone',
        'company',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'read_at',
        'replied_at',
        'admin_notes'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'status' => 'read',
            'read_at' => now()
        ]);
    }

    /**
     * Mark message as replied
     */
    public function markAsReplied(): void
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now()
        ]);
    }

    /**
     * Check if message is new
     */
    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    /**
     * Get status color for admin panel
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new' => 'success',
            'read' => 'warning', 
            'replied' => 'primary',
            'closed' => 'secondary',
            default => 'secondary'
        };
    }
}
