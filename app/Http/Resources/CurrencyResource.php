<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Currency",
 *     title="Currency",
 *     description="Currency data",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="USD", description="3 karakterli para birimi kodu"),
 *     @OA\Property(property="name", type="string", example="US Dollar", description="Para birimi adı"),
 *     @OA\Property(property="symbol", type="string", example="$", description="Para birimi sembolü"),
 *     @OA\Property(property="exchange_rate", type="number", format="float", example=29.45, description="TRY cinsinden döviz kuru"),
 *     @OA\Property(property="is_default", type="boolean", example=false, description="Varsayılan para birimi mi?"),
 *     @OA\Property(property="is_active", type="boolean", example=true, description="Aktif durumda mı?"),
 *     @OA\Property(property="formatted_rate", type="string", example="1 USD = 29.45 TRY", description="Formatlanmış kur bilgisi"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-08T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-08T10:30:00Z")
 * )
 */
class CurrencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'exchange_rate' => (float) $this->exchange_rate,
            'is_default' => (bool) $this->is_default,
            'is_active' => (bool) $this->is_active,
            'formatted_rate' => $this->getFormattedRate(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get formatted exchange rate string
     */
    private function getFormattedRate(): string
    {
        if ($this->is_default) {
            return "1 {$this->code} = 1 {$this->code} (Base Currency)";
        }

        return "1 {$this->code} = {$this->exchange_rate} TRY";
    }
}