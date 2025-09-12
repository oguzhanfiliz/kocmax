<?php

declare(strict_types=1);

namespace App\ValueObjects\Pricing;

use InvalidArgumentException;

/**
 * İndirim değer nesnesi.
 *
 * Yüzdesel veya sabit tutar indirimlerini temsil eder ve
 * fiyat nesnesi üzerinde indirim hesaplamalarını gerçekleştirir.
 */
class Discount
{
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED_AMOUNT = 'fixed_amount';

    private readonly float $value;
    private readonly string $type;
    private ?string $currency;
    private readonly string $name;
    private readonly ?string $description;
    private readonly int $priority;

    /**
     * Yapıcı metot.
     *
     * @param float $value İndirim değeri (yüzde veya tutar)
     * @param string $type İndirim tipi (percentage|fixed_amount)
     * @param string $name İndirim adı
     * @param string|null $description Açıklama
     * @param int $priority Öncelik
     */
    public function __construct(
        float $value,
        string $type = self::TYPE_PERCENTAGE,
        string $name = 'Discount',
        ?string $description = null,
        int $priority = 0
    ) {
        if ($value < 0) {
            if ($type === self::TYPE_PERCENTAGE) {
                throw new InvalidArgumentException('Yüzde değeri 0 ile 100 arasında olmalıdır');
            }
            throw new InvalidArgumentException('Sabit tutar negatif olamaz');
        }

        if (!in_array($type, [self::TYPE_PERCENTAGE, self::TYPE_FIXED_AMOUNT])) {
            throw new InvalidArgumentException('Geçersiz indirim tipi');
        }

        if ($type === self::TYPE_PERCENTAGE && ($value < 0 || $value > 100)) {
            throw new InvalidArgumentException('Yüzde değeri 0 ile 100 arasında olmalıdır');
        }

        $this->value = $value;
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->priority = $priority;
        $this->currency = null;
    }

    /**
     * İndirim değerini döndürür.
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * İndirim tipini döndürür.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * İndirim adını döndürür.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * İndirim açıklamasını döndürür.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Öncelik değerini döndürür.
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Yüzdesel indirim mi?
     */
    public function isPercentage(): bool
    {
        return $this->type === self::TYPE_PERCENTAGE;
    }

    /**
     * Sabit tutar indirimi mi?
     */
    public function isFixedAmount(): bool
    {
        return $this->type === self::TYPE_FIXED_AMOUNT;
    }

    /**
     * İndirimi verilen fiyata uygular ve yeni fiyat döndürür.
     *
     * @param Price $originalPrice Orijinal fiyat
     * @return Price İndirim uygulanmış fiyat
     */
    public function apply(Price $originalPrice): Price
    {
        if ($this->isPercentage()) {
            return $originalPrice->applyDiscount($this->value);
        }

        // Sabit tutar indirimi
        if ($this->currency !== null && $this->currency !== $originalPrice->getCurrency()) {
            throw new InvalidArgumentException('Para birimi uyuşmazlığı');
        }
        $discountAmount = new Price($this->value, $originalPrice->getCurrency());
        return $originalPrice->subtract($discountAmount);
    }

    /**
     * İndirim tutarını hesaplar ve döndürür.
     *
     * @param Price $originalPrice Orijinal fiyat
     * @return Price İndirim tutarı (fiyat nesnesi)
     */
    public function calculateDiscountAmount(Price $originalPrice): Price
    {
        if ($this->isPercentage()) {
            return $originalPrice->percentage($this->value);
        }

        // Sabit tutar indirimi
        if ($this->currency !== null && $this->currency !== $originalPrice->getCurrency()) {
            throw new InvalidArgumentException('Para birimi uyuşmazlığı');
        }
        $fixedAmount = new Price($this->value, $originalPrice->getCurrency());
        
        // İndirim tutarının orijinal fiyatı aşmasına izin verme
        if ($fixedAmount->isGreaterThan($originalPrice)) {
            return $originalPrice;
        }
        
        return $fixedAmount;
    }

    /**
     * İndirim uygulanabilir mi kontrol eder.
     *
     * @param Price $price Ürün fiyatı
     * @param int $quantity Adet
     * @return bool Uygulanabilirse true
     */
    public function canApplyTo(Price $price, int $quantity = 1): bool
    {
        if ($this->isFixedAmount()) {
            $totalPrice = $price->multiply($quantity);
            $discountAmount = new Price($this->value, $price->getCurrency());
            return $totalPrice->isGreaterThan($discountAmount);
        }

        // Yüzdesel indirimler her zaman uygulanabilir
        return true;
    }

    /**
     * İndirim değerini insanlar için okunabilir biçimde döndürür.
     */
    public function format(): string
    {
        if ($this->isPercentage()) {
            return number_format($this->value, 1) . '%';
        }

        $currency = $this->currency ?? 'TRY';
        return number_format($this->value, 1) . ' ' . $currency;
    }

    /**
     * Dizi temsiline dönüştürür.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'formatted' => $this->format()
        ];
    }

    /**
     * Yüzdesel indirim örneği oluşturur.
     */
    public static function percentage(float $percentage, string $name = 'Early bird discount', ?string $description = null, int $priority = 0): self
    {
        return new self($percentage, self::TYPE_PERCENTAGE, $name, $description, $priority);
    }

    /**
     * Sabit tutarlı indirim örneği oluşturur.
     */
    public static function fixedAmount(float $amount, string $currency = 'TRY', string $name = 'Fixed Amount Discount', ?string $description = null, int $priority = 0): self
    {
        $instance = new self($amount, self::TYPE_FIXED_AMOUNT, $name, $description, $priority);
        $instance->currency = strtoupper($currency);
        return $instance;
    }

    /**
     * Metin temsili.
     */
    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * Sabit tutar indirim kontrolü (geriye dönük uyumluluk için kısayol).
     */
    public function isFixed(): bool
    {
        return $this->isFixedAmount();
    }

    /**
     * İndirimin para birimini döndürür (yalnızca sabit tutar için).
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }
}