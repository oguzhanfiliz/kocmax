<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Feed\GoogleMerchant\GoogleMerchantFeedService;
use Illuminate\Console\Command;
use Throwable;

class GenerateGoogleMerchantFeed extends Command
{
    protected $signature = 'merchant:generate-feed {--disk=} {--path=}';

    protected $description = 'Generate Google Merchant XML feed and persist it to storage.';

    public function __construct(private readonly GoogleMerchantFeedService $feedService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting Google Merchant feed generation...');

        $disk = $this->option('disk');
        $path = $this->option('path');

        try {
            $result = $this->feedService->generate($disk, $path);
        } catch (Throwable $throwable) {
            $this->error('Feed generation failed: ' . $throwable->getMessage());
            report($throwable);

            return self::FAILURE;
        }

        if (!$result->isSuccess()) {
            $this->warn(sprintf(
                'Feed generation incomplete. items=%d skipped=%d errors=%d',
                $result->getItemCount(),
                $result->getSkippedCount(),
                count($result->getErrors())
            ));

            foreach ($result->getErrors() as $message) {
                $this->line('- ' . $message);
            }

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Feed created successfully. items=%d skipped=%d path=%s (%.2fs)',
            $result->getItemCount(),
            $result->getSkippedCount(),
            $result->getPath(),
            $result->getDuration()
        ));

        return self::SUCCESS;
    }
}

