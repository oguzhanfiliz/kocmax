<?php

declare(strict_types=1);

namespace App\Services\Feed\GoogleMerchant;

use App\Models\ProductVariant;
use App\Services\Feed\GoogleMerchant\DTO\FeedGenerationResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GoogleMerchantFeedService
{
    private const CHUNK_SIZE = 250;

    public function __construct(
        private readonly GoogleMerchantFeedMapper $mapper,
        private readonly GoogleMerchantFeedWriter $writer
    ) {
    }

    public function generate(?string $disk = null, ?string $path = null): FeedGenerationResult
    {
        $startedAt = microtime(true);
        $generatedAt = Carbon::now()->toDateTimeImmutable();

        if (!config('feeds.google_merchant.enabled', true)) {
            return new FeedGenerationResult(
                success: false,
                items: 0,
                skipped: 0,
                path: null,
                duration: microtime(true) - $startedAt,
                errors: ['Google Merchant feed generation is disabled by configuration.'],
                generatedAt: $generatedAt
            );
        }

        $disk = $disk ?? config('feeds.google_merchant.storage_disk', 'public');
        $path = $path ?? config('feeds.google_merchant.storage_path', 'feeds/google-merchant.xml');

        $items = [];
        $skipped = 0;
        $errors = [];

        $limit = config('feeds.google_merchant.item_limit');
        $processed = 0;

        ProductVariant::query()
            ->with($this->variantRelations())
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->whereHas('product', function (Builder $builder) {
                $builder
                    ->where('is_active', true)
                    ->whereNull('deleted_at');
            })
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function ($variants) use (&$items, &$skipped, &$errors, $limit, &$processed) {
                foreach ($variants as $variant) {
                    if ($limit !== null && $processed >= (int) $limit) {
                        return false; // Stop chunking when limit is reached
                    }

                    try {
                        $item = $this->mapper->map($variant);
                    } catch (Throwable $throwable) {
                        $errors[] = sprintf('Variant %d: %s', $variant->id, $throwable->getMessage());
                        Log::error('Failed to map Merchant feed item', [
                            'variant_id' => $variant->id,
                            'product_id' => $variant->product?->id,
                            'exception' => $throwable,
                        ]);
                        $skipped++;
                        continue;
                    }

                    if (!$item) {
                        $skipped++;
                        continue;
                    }

                    $items[] = $item;
                    $processed++;
                }
            });

        if (empty($items)) {
            $duration = microtime(true) - $startedAt;
            return new FeedGenerationResult(
                success: false,
                items: 0,
                skipped: $skipped,
                path: null,
                duration: $duration,
                errors: array_merge($errors, ['No eligible products found for Google Merchant feed.']),
                generatedAt: $generatedAt
            );
        }

        $this->backupExistingFeed($disk, $path);

        try {
            $writtenPath = $this->writer->write($items, $disk, $path);
        } catch (Throwable $throwable) {
            $duration = microtime(true) - $startedAt;
            $errors[] = 'Failed to write Google Merchant feed: ' . $throwable->getMessage();
            Log::error('Failed to write Google Merchant feed', [
                'disk' => $disk,
                'path' => $path,
                'exception' => $throwable,
            ]);

            return new FeedGenerationResult(
                success: false,
                items: count($items),
                skipped: $skipped,
                path: null,
                duration: $duration,
                errors: $errors,
                generatedAt: $generatedAt
            );
        }

        $duration = microtime(true) - $startedAt;

        return new FeedGenerationResult(
            success: true,
            items: count($items),
            skipped: $skipped,
            path: $writtenPath,
            duration: $duration,
            errors: $errors,
            generatedAt: $generatedAt
        );
    }

    private function backupExistingFeed(string $disk, string $path): void
    {
        $filesystem = Storage::disk($disk);
        if (!$filesystem->exists($path)) {
            return;
        }

        $timestamp = Carbon::now()->format('YmdHis');
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $basePath = $extension ? substr($path, 0, -(strlen($extension) + 1)) : $path;
        $backupPath = sprintf('%s.%s.%s', $basePath, $timestamp, $extension ?: 'xml');

        try {
            $filesystem->copy($path, $backupPath);
        } catch (Throwable $throwable) {
            Log::warning('Unable to backup existing Merchant feed file', [
                'disk' => $disk,
                'path' => $path,
                'backup_path' => $backupPath,
                'exception' => $throwable,
            ]);
        }
    }

    private function variantRelations(): array
    {
        return [
            'images' => fn ($query) => $query->ordered(),
            'primaryImage',
            'product' => function ($query) {
                return $query->with([
                    'categories',
                    'images' => fn ($imageQuery) => $imageQuery->ordered(),
                    'primaryImage',
                ]);
            },
        ];
    }
}
