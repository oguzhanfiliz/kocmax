<?php

declare(strict_types=1);

namespace App\ValueObjects\Pricing;

use InvalidArgumentException;

/**
 * Fiyat değer nesnesi.
 *
 * Para birimi ve tutarı birlikte ele alır; toplama, çıkarma, çarpma,
 * yüzde ve indirim uygulama gibi işlemleri güvenli şekilde sağlar.
 */
class Price
{
    private readonly float $amount;
    private readonly string $currency;

    /**
     * Yapıcı metot.
     *
     * @param float $amount Tutar
     * @param string $currency Para birimi (varsayılan: TRY)
     */
    public function __construct(float $amount, string $currency = 'TRY')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Tutar negatif olamaz');
        }

        if (empty($currency)) {
            throw new InvalidArgumentException('Para birimi boş olamaz');
        }

        $this->amount = round($amount, 2);
        $this->currency = strtoupper($currency);
    }

    /**
     * Tutarı döndürür.
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Para birimini döndürür.
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Başka bir fiyatla aynı para biriminde toplam alır.
     */
    public function add(Price $other): self
    {
        $this->ensureSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    /**
     * Başka bir fiyatı aynı para biriminde düşer. Sonuç negatife inmez.
     */
    public function subtract(Price $other): self
    {
        $this->ensureSameCurrency($other);
        $newAmount = $this->amount - $other->amount;
        
        if ($newAmount < 0) {
            $newAmount = 0;
        }
        
        return new self($newAmount, $this->currency);
    }

    /**
     * Fiyatı katsayı ile çarpar.
     *
     * @param float $multiplier Katsayı (negatif olamaz)
     */
    public function multiply(float $multiplier): self
    {
        if ($multiplier < 0) {
            throw new InvalidArgumentException('Katsayı negatif olamaz');
        }
        
        return new self($this->amount * $multiplier, $this->currency);
    }

    /**
     * Verilen yüzde oranının tutar karşılığını döndürür.
     *
     * @param float $percentage 0-100 arası yüzde
     */
    public function percentage(float $percentage): self
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Yüzde 0-100 arası olmalıdır');
        }
        
        return new self($this->amount * ($percentage / 100), $this->currency);
    }

    /**
     * Verilen yüzde oranında indirim uygular ve yeni fiyat döndürür.
     *
     * @param float $discountPercentage 0-100 arası indirim yüzdesi
     */
    public function applyDiscount(float $discountPercentage): self
    {
        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new InvalidArgumentException('İndirim yüzdesi 0-100 arası olmalıdır');
        }
        
        $discountAmount = $this->amount * ($discountPercentage / 100);
        return new self($this->amount - $discountAmount, $this->currency);
    }

    /**
     * Fiyat eşitliği kontrolü.
     */
    public function equals(Price $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    /**
     * Verilen fiyattan büyük mü?
     */
    public function isGreaterThan(Price $other): bool
    {
        $this->ensureSameCurrency($other);
        return $this->amount > $other->amount;
    }

    /**
     * Verilen fiyattan küçük mü?
     */
    public function isLessThan(Price $other): bool
    {
        $this->ensureSameCurrency($other);
        return $this->amount < $other->amount;
    }

    /**
     * Tutar sıfır mı?
     */
    public function isZero(): bool
    {
        return $this->amount === 0.0;
    }

    /**
     * Para birimi sembolü ile görüntülenebilir formatı döndürür.
     */
    public function format(): string
    {
        $symbol = $this->getCurrencySymbol();
        // TR locale biçimi beklendiği için: 1.234,56 ₺
        $formatted = number_format($this->amount, 2, ',', '.');
        return $formatted . ' ' . trim($symbol);
    }

    /**
     * Ekranda gösterim için formatlanmış değer.
     */
    public function formatForDisplay(): string
    {
        return $this->format();
    }

    /**
     * Para birimi için sembolü döndürür.
     */
    private function getCurrencySymbol(): string
    {
        return match($this->currency) {
            'TRY' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $this->currency
        };
    }

    /**
     * Para birimleri uyuşmazsa istisna fırlatır.
     */
    private function ensureSameCurrency(Price $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Farklı para birimlerinde işlem yapılamaz');
        }
    }

    /**
     * Dizi temsili.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'formatted' => $this->format(),
            'display' => $this->formatForDisplay()
        ];
    }

    /**
     * Metin temsili.
     */
    public function __toString(): string
    {
        return $this->format();
    }
}