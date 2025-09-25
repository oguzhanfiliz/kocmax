<?php

declare(strict_types=1);

namespace App\Services\Feed\GoogleMerchant\DTO;

/**
 * Data transfer object representing a single Google Merchant feed item.
 */
class FeedItem
{
    /** @var array<string, string|array<int, string>> */
    private array $attributes;

    /**
     * @param string $id Merchant item identifier (g:id)
     * @param string $title Product title (title)
     * @param string $description Product description (description)
     * @param string $link Product canonical URL (link)
     * @param string $imageLink Primary image URL (g:image_link)
     * @param string $availability Availability status (g:availability)
     * @param array{amount:string,currency:string} $price Formatted price payload
     * @param array{amount:string,currency:string}|null $salePrice Optional sale price payload
     * @param string|null $itemGroupId Group identifier for variants (g:item_group_id)
     * @param array<int, string> $additionalImageLinks Collection of supplementary image URLs
     * @param array<string, string|array<int, string>> $attributes Optional Google attributes keyed without the namespace prefix
     */
    public function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly string $description,
        private readonly string $link,
        private readonly string $imageLink,
        private readonly string $availability,
        private readonly array $price,
        private readonly ?array $salePrice = null,
        private readonly ?string $itemGroupId = null,
        private array $additionalImageLinks = [],
        array $attributes = []
    ) {
        $this->attributes = $attributes;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getImageLink(): string
    {
        return $this->imageLink;
    }

    public function getAvailability(): string
    {
        return $this->availability;
    }

    /**
     * @return array{amount:string,currency:string}
     */
    public function getPrice(): array
    {
        return $this->price;
    }

    /**
     * @return array{amount:string,currency:string}|null
     */
    public function getSalePrice(): ?array
    {
        return $this->salePrice;
    }

    public function getItemGroupId(): ?string
    {
        return $this->itemGroupId;
    }

    /**
     * @return array<int, string>
     */
    public function getAdditionalImageLinks(): array
    {
        return $this->additionalImageLinks;
    }

    /**
     * @param array<int, string> $links
     */
    public function setAdditionalImageLinks(array $links): void
    {
        $this->additionalImageLinks = $links;
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name Attribute without namespace (e.g. `brand`, `gtin`).
     * @param string|array<int, string> $value Attribute value(s).
     */
    public function setAttribute(string $name, string|array $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function hasSalePrice(): bool
    {
        return $this->salePrice !== null;
    }
}

