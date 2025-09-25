<?php

declare(strict_types=1);

namespace App\Services\Feed\GoogleMerchant;

use App\Services\Feed\GoogleMerchant\DTO\FeedItem;
use DateTimeImmutable;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Facades\Storage;

class GoogleMerchantFeedWriter
{
    private const GOOGLE_NAMESPACE = 'http://base.google.com/ns/1.0';

    public function __construct(private readonly ?string $storageDisk = null, private readonly ?string $storagePath = null)
    {
    }

    /**
     * @param iterable<int, FeedItem> $items
     */
    public function generate(iterable $items): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $rss = $doc->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:g', self::GOOGLE_NAMESPACE);
        $doc->appendChild($rss);

        $channel = $doc->createElement('channel');
        $rss->appendChild($channel);

        $channelConfig = config('feeds.google_merchant.channel', []);
        $channel->appendChild($this->createCdataElement($doc, 'title', $channelConfig['title'] ?? config('app.name')));
        $channel->appendChild($this->createTextElement($doc, 'link', $channelConfig['link'] ?? config('feeds.google_merchant.product_url_base', config('app.url'))));
        $channel->appendChild($this->createCdataElement($doc, 'description', $channelConfig['description'] ?? (config('app.name') . ' product feed')));

        if (isset($channelConfig['language'])) {
            $channel->appendChild($this->createTextElement($doc, 'language', $channelConfig['language']));
        }

        $now = new DateTimeImmutable();
        $channel->appendChild($this->createTextElement($doc, 'lastBuildDate', $now->format(DATE_RSS)));

        foreach ($items as $item) {
            $itemNode = $doc->createElement('item');
            $channel->appendChild($itemNode);

            $itemNode->appendChild($this->createGoogleElement($doc, 'id', $item->getId()));
            $itemNode->appendChild($this->createCdataElement($doc, 'title', $item->getTitle()));
            $itemNode->appendChild($this->createCdataElement($doc, 'description', $item->getDescription()));
            $itemNode->appendChild($this->createTextElement($doc, 'link', $item->getLink()));
            $itemNode->appendChild($this->createGoogleElement($doc, 'image_link', $item->getImageLink()));

            foreach ($item->getAdditionalImageLinks() as $image) {
                $itemNode->appendChild($this->createGoogleElement($doc, 'additional_image_link', $image));
            }

            $itemNode->appendChild($this->createGoogleElement($doc, 'availability', $item->getAvailability()));
            $itemNode->appendChild($this->createGoogleElement($doc, 'price', $this->formatPrice($item->getPrice())));

            if ($item->hasSalePrice()) {
                $itemNode->appendChild($this->createGoogleElement($doc, 'sale_price', $this->formatPrice($item->getSalePrice())));
            }

            if ($item->getItemGroupId()) {
                $itemNode->appendChild($this->createGoogleElement($doc, 'item_group_id', (string) $item->getItemGroupId()));
            }

            foreach ($item->getAttributes() as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $single) {
                        $this->appendGoogleAttribute($doc, $itemNode, $name, $single);
                    }
                    continue;
                }

                $this->appendGoogleAttribute($doc, $itemNode, $name, $value);
            }
        }

        return $doc->saveXML() ?: '';
    }

    /**
     * @param iterable<int, FeedItem> $items
     */
    public function write(iterable $items, ?string $disk = null, ?string $path = null): string
    {
        $xml = $this->generate($items);

        $disk = $disk ?? $this->storageDisk ?? config('feeds.google_merchant.storage_disk', 'public');
        $path = $path ?? $this->storagePath ?? config('feeds.google_merchant.storage_path', 'feeds/google-merchant.xml');

        Storage::disk($disk)->put($path, $xml, ['visibility' => 'public']);

        return $path;
    }

    private function createCdataElement(DOMDocument $doc, string $name, string $value): DOMElement
    {
        $element = $doc->createElement($name);
        $element->appendChild($doc->createCDATASection((string) $value));

        return $element;
    }

    private function createTextElement(DOMDocument $doc, string $name, string $value): DOMElement
    {
        $element = $doc->createElement($name);
        $element->appendChild($doc->createTextNode((string) $value));

        return $element;
    }

    private function createGoogleElement(DOMDocument $doc, string $name, string $value): DOMElement
    {
        $element = $doc->createElementNS(self::GOOGLE_NAMESPACE, 'g:' . $name);
        $element->appendChild($doc->createTextNode((string) $value));

        return $element;
    }

    private function appendGoogleAttribute(DOMDocument $doc, DOMElement $parent, string $name, string $value): void
    {
        if ($value === '') {
            return;
        }

        $parent->appendChild($this->createGoogleElement($doc, $name, $value));
    }

    /**
     * @param array{amount:string,currency:string}|null $price
     */
    private function formatPrice(?array $price): string
    {
        if (!$price) {
            return '';
        }

        return $price['amount'] . ' ' . strtoupper($price['currency']);
    }
}
