<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Feed\GoogleMerchant;

use App\Enums\Pricing\CustomerType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantImage;
use App\Services\Feed\GoogleMerchant\GoogleMerchantFeedMapper;
use App\Services\MultiCurrencyPricingService;
use App\ValueObjects\Pricing\Discount;
use App\ValueObjects\Pricing\Price;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class GoogleMerchantFeedMapperTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_maps_active_variant_into_feed_item(): void
    {
        config()->set('feeds.google_merchant', array_merge(
            config('feeds.google_merchant') ?? [],
            [
                'product_url_base' => 'https://shop.test',
                'product_url_prefix' => 'urun',
                'target_currency' => 'TRY',
                'default_google_product_category' => 'Test Category',
                'max_additional_images' => 5,
            ]
        ));

        $product = new Product();
        $product->id = 1;
        $product->slug = 'test-urun';
        $product->name = 'Test Ürün';
        $product->description = '<p>Açıklama <strong>HTML</strong></p>';
        $product->short_description = 'Kısa açıklama';
        $product->is_active = true;
        $product->is_featured = true;
        $product->is_new = true;
        $product->is_bestseller = true;
        $product->gender = 'female';

        $category = new Category();
        $category->id = 10;
        $category->name = 'Elektronik';
        $category->sort_order = 1;
        $category->slug = 'elektronik';
        $product->setRelation('categories', collect([$category]));

        $product->setRelation('images', collect([
            (new ProductImage())->forceFill([
                'image' => 'products/test.jpg',
                'is_primary' => true,
            ]),
        ]));
        $product->setRelation('primaryImage', null);

        $variant = new ProductVariant();
        $variant->id = 55;
        $variant->product_id = $product->id;
        $variant->sku = 'SKU-001';
        $variant->barcode = '1234567890123';
        $variant->price = 200.00;
        $variant->stock = 15;
        $variant->name = 'Mavi';
        $variant->color = 'Mavi';
        $variant->size = 'L';
        $variant->package_weight = 1.25;
        $variant->package_length = 32.5;
        $variant->package_width = 22.0;
        $variant->package_height = 12.0;
        $variant->is_active = true;
        $variant->setRelation('product', $product);

        $primaryImage = new VariantImage();
        $primaryImage->image_url = 'https://cdn.test/images/variant-primary.jpg';
        $primaryImage->is_primary = true;

        $secondaryImage = new VariantImage();
        $secondaryImage->image_url = 'https://cdn.test/images/variant-secondary.jpg';
        $secondaryImage->is_primary = false;

        $variant->setRelation('images', collect([$primaryImage, $secondaryImage]));
        $variant->setRelation('primaryImage', $primaryImage);

        $pricingService = Mockery::mock(MultiCurrencyPricingService::class);
        $priceResult = new PriceResult(
            originalPrice: new Price(200, 'TRY'),
            finalPrice: new Price(150, 'TRY'),
            appliedDiscounts: new Collection([new Discount(25.0)]),
            customerType: CustomerType::B2C
        );

        $pricingService
            ->shouldReceive('calculatePrice')
            ->once()
            ->andReturn($priceResult);

        $mapper = new GoogleMerchantFeedMapper($pricingService);
        $feedItem = $mapper->map($variant);

        $this->assertNotNull($feedItem);
        $this->assertSame('SKU-001', $feedItem->getId());
        $this->assertSame('https://shop.test/urun/test-urun', $feedItem->getLink());
        $this->assertSame('Test Ürün - Mavi', $feedItem->getTitle());
        $this->assertSame('in stock', $feedItem->getAvailability());
        $this->assertSame(['amount' => '200.00', 'currency' => 'TRY'], $feedItem->getPrice());
        $this->assertSame(['amount' => '150.00', 'currency' => 'TRY'], $feedItem->getSalePrice());
        $this->assertSame('https://cdn.test/images/variant-primary.jpg', $feedItem->getImageLink());
        $this->assertCount(1, $feedItem->getAdditionalImageLinks());

        $attributes = $feedItem->getAttributes();
        $this->assertSame('new', $attributes['condition']);
        $this->assertSame('1234567890123', $attributes['gtin']);
        $this->assertSame('SKU-001', $attributes['mpn']);
        $this->assertSame('Test Category', $attributes['google_product_category']);
        $this->assertSame('Elektronik', $attributes['product_type']);
        $this->assertSame('KOCMAX', $attributes['brand']);
        $this->assertSame('Mavi', $attributes['color']);
        $this->assertSame('L', $attributes['size']);
        $this->assertSame('female', $attributes['gender']);
        $this->assertSame('featured', $attributes['custom_label_0']);
        $this->assertSame('new', $attributes['custom_label_1']);
        $this->assertSame('bestseller', $attributes['custom_label_2']);
        $this->assertSame('1.25 kg', $attributes['shipping_weight']);
        $this->assertSame('32.5 cm', $attributes['shipping_length']);
        $this->assertSame('22.0 cm', $attributes['shipping_width']);
        $this->assertSame('12.0 cm', $attributes['shipping_height']);
    }

    public function test_it_returns_null_when_product_or_variant_inactive(): void
    {
        $pricingService = Mockery::mock(MultiCurrencyPricingService::class);
        $mapper = new GoogleMerchantFeedMapper($pricingService);

        $product = new Product(['is_active' => false]);
        $variant = new ProductVariant(['is_active' => false]);
        $variant->setRelation('product', $product);

        $this->assertNull($mapper->map($variant));
    }
}
