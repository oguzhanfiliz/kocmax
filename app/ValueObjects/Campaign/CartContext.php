<?php

declare(strict_types=1);

namespace App\ValueObjects\Campaign;

use Illuminate\Support\Collection;

/**
 * Sepet bağlamı değer nesnesi.
 *
 * Sepetteki kalemler, toplam tutar, müşteri tipi/ID ve ek metaverileri taşır.
 */
class CartContext
{
    /**
     * Yapıcı metot.
     *
     * @param Collection $items Sepet kalemleri (ürün, varyant, adet vb.)
     * @param float $totalAmount Sepet toplam tutarı
     * @param string $customerType Müşteri tipi (örn: guest, B2C, B2B)
     * @param int|null $customerId Müşteri ID (opsiyonel)
     * @param array $metadata Ek metaveriler
     */
    public function __construct(
        private readonly Collection $items, // Ürün, varyant ve adet bilgilerini içeren sepet kalemleri
        private readonly float $totalAmount,
        private readonly string $customerType = 'guest',
        private readonly ?int $customerId = null,
        private readonly array $metadata = []
    ) {}

    /**
     * Sepet kalemlerini döndürür.
     *
     * @return Collection<array>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Sepet toplam tutarını döndürür.
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * Müşteri tipini döndürür.
     */
    public function getCustomerType(): string
    {
        return $this->customerType;
    }

    /**
     * Müşteri ID bilgisini döndürür.
     */
    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    /**
     * Ek metaverileri döndürür.
     *
     * @return array<string,mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Belirli bir ürün ID'sine ait sepet kalemlerini döndürür.
     *
     * @param int $productId Ürün ID
     * @return Collection<array>
     */
    public function getItemsByProduct(int $productId): Collection
    {
        return $this->items->filter(function ($item) use ($productId) {
            return $item['product_id'] === $productId;
        });
    }

    /**
     * Sepetteki toplam ürün adedini döndürür.
     */
    public function getTotalQuantity(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Belirli bir ürün için toplam adedi döndürür.
     *
     * @param int $productId Ürün ID
     */
    public function getTotalQuantityForProduct(int $productId): int
    {
        return $this->getItemsByProduct($productId)->sum('quantity');
    }

    /**
     * Sepette belirtilen ürün ID'si var mı?
     *
     * @param int $productId Ürün ID
     */
    public function hasProduct(int $productId): bool
    {
        return $this->items->contains(function ($item) use ($productId) {
            return $item['product_id'] === $productId;
        });
    }

    /**
     * Sepette verilen ürün ID'lerinin hepsi var mı?
     *
     * @param array<int> $productIds Ürün ID listesi
     */
    public function hasProducts(array $productIds): bool
    {
        foreach ($productIds as $productId) {
            if (!$this->hasProduct($productId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Sepette bulunan benzersiz ürün ID'lerini döndürür.
     *
     * @return array<int>
     */
    public function getProductIds(): array
    {
        return $this->items->pluck('product_id')->unique()->toArray();
    }

    /**
     * Belirli bir kategoriye ait sepet kalemlerini döndürür.
     *
     * @param int $categoryId Kategori ID
     * @return Collection<array>
     */
    public function getItemsInCategory(int $categoryId): Collection
    {
        return $this->items->filter(function ($item) use ($categoryId) {
            return in_array($categoryId, $item['category_ids'] ?? []);
        });
    }

    /**
     * Metaveriye bir anahtar/değer ekleyerek yeni bir bağlam örneği döndürür (immutability).
     *
     * @param string $key Anahtar
     * @param mixed $value Değer
     */
    public function withMetadata(string $key, mixed $value): self
    {
        $metadata = array_merge($this->metadata, [$key => $value]);
        
        return new self(
            $this->items,
            $this->totalAmount,
            $this->customerType,
            $this->customerId,
            $metadata
        );
    }

    /**
     * Sepet verilerinden kolay oluşturma için fabrikasyon metodu.
     */
    public static function fromCart(array $cartData): self
    {
        $items = collect($cartData['items'] ?? []);
        $totalAmount = (float) ($cartData['total'] ?? 0);
        $customerType = $cartData['customer_type'] ?? 'guest';
        $customerId = $cartData['customer_id'] ?? null;
        
        return new self($items, $totalAmount, $customerType, $customerId, $cartData);
    }

    /**
     * Kalemler dizisinden bağlam oluşturma için fabrikasyon metodu.
     */
    public static function fromItems(array $items, float $totalAmount, string $customerType = 'guest', ?int $customerId = null, array $metadata = []): self
    {
        return new self(collect($items), $totalAmount, $customerType, $customerId, $metadata);
    }

    /**
     * Loglama/hata ayıklama için dizi temsiline dönüştürür.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'items' => $this->items->toArray(),
            'total_amount' => $this->totalAmount,
            'customer_type' => $this->customerType,
            'customer_id' => $this->customerId,
            'metadata' => $this->metadata,
        ];
    }
}