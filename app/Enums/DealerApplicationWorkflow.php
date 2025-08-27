<?php
declare(strict_types=1);

namespace App\Enums;

enum DealerApplicationWorkflow: string
{
    case GUEST_REGISTRATION = 'guest_registration';
    case USER_REGISTRATION = 'user_registration';
    case APPROVAL_REGISTRATION = 'approval_registration';

    /**
     * Get the display label for the workflow type.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::GUEST_REGISTRATION => 'Misafir Kayıt',
            self::USER_REGISTRATION => 'Kullanıcı Kayıt',
            self::APPROVAL_REGISTRATION => 'Onay Kayıt',
        };
    }

    /**
     * Get description for the workflow type.
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::GUEST_REGISTRATION => 'Başvuru sırasında yeni kullanıcı oluşturulur',
            self::USER_REGISTRATION => 'Var olan kullanıcı başvuru yapar',
            self::APPROVAL_REGISTRATION => 'Onaylandığında kullanıcı oluşturulur',
        };
    }

    /**
     * Check if user should be created during application.
     */
    public function shouldCreateUserOnApplication(): bool
    {
        return $this === self::GUEST_REGISTRATION;
    }

    /**
     * Check if user should be created on approval.
     */
    public function shouldCreateUserOnApproval(): bool
    {
        return $this === self::APPROVAL_REGISTRATION;
    }

    /**
     * Check if user must exist before application.
     */
    public function requiresExistingUser(): bool
    {
        return $this === self::USER_REGISTRATION;
    }
}