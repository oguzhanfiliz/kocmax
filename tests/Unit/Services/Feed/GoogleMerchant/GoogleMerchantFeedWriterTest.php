<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Feed\GoogleMerchant;

use App\Services\Feed\GoogleMerchant\DTO\FeedItem;
use App\Services\Feed\GoogleMerchant\GoogleMerchantFeedWriter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GoogleMerchantFeedWriterTest extends TestCase
{
    public function test_generate_creates_valid_rss_structure(): void
    {
        $writer = new GoogleMerchantFeedWriter();
        $item = new FeedItem(
            id: 'SKU-001',
            title: 'Test Ürün',
            description: 'Açıklama',
            link: 'https://shop.test/urun/test-urun',
            imageLink: 'https://cdn.test/images/1.jpg',
            availability: 'in stock',
            price: ['amount' => '100.00', 'currency' => 'TRY'],
            salePrice: ['amount' => '90.00', 'currency' => 'TRY'],
            itemGroupId: '123',
            additionalImageLinks: ['https://cdn.test/images/1-1.jpg'],
            attributes: [
                'condition' => 'new',
                'brand' => 'Test Marka',
                'gtin' => '1234567890123',
                'custom_label_0' => 'featured',
            ]
        );

        $xml = $writer->generate([$item]);

        $this->assertNotEmpty($xml);
        $feed = simplexml_load_string($xml);
        $this->assertNotFalse($feed, 'XML parse failed');
        $namespaces = $feed->getNamespaces(true);
        $this->assertArrayHasKey('g', $namespaces);

        $firstItem = $feed->channel->item[0];
        $this->assertSame('SKU-001', (string) $firstItem->children($namespaces['g'])->id);
        $this->assertSame('Test Ürün', (string) $firstItem->title);
        $this->assertSame('https://cdn.test/images/1.jpg', (string) $firstItem->children($namespaces['g'])->image_link);
        $this->assertSame('100.00 TRY', (string) $firstItem->children($namespaces['g'])->price);
        $this->assertSame('90.00 TRY', (string) $firstItem->children($namespaces['g'])->sale_price);
        $this->assertSame('featured', (string) $firstItem->children($namespaces['g'])->custom_label_0);
    }

    public function test_write_persists_feed_to_storage(): void
    {
        Storage::fake('public');
        $writer = new GoogleMerchantFeedWriter('public', 'feeds/test.xml');

        $item = new FeedItem(
            id: 'SKU-001',
            title: 'Test Ürün',
            description: 'Açıklama',
            link: 'https://shop.test/urun/test-urun',
            imageLink: 'https://cdn.test/images/1.jpg',
            availability: 'in stock',
            price: ['amount' => '100.00', 'currency' => 'TRY']
        );

        $writer->write([$item]);

        Storage::disk('public')->assertExists('feeds/test.xml');
        $contents = Storage::disk('public')->get('feeds/test.xml');
        $this->assertStringContainsString('<g:id>SKU-001</g:id>', $contents);
    }
}

