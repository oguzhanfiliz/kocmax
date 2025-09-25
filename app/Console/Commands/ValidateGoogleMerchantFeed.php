<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ValidateGoogleMerchantFeed extends Command
{
    protected $signature = 'merchant:validate-feed {--disk=} {--path=}';

    protected $description = 'Validate the Google Merchant feed XML for well-formedness and namespace requirements.';

    public function handle(): int
    {
        $disk = $this->option('disk') ?? config('feeds.google_merchant.storage_disk', 'public');
        $path = $this->option('path') ?? config('feeds.google_merchant.storage_path', 'feeds/google-merchant.xml');

        $filesystem = Storage::disk($disk);

        if (!$filesystem->exists($path)) {
            $this->error(sprintf('Feed file not found on disk "%s": %s', $disk, $path));
            return self::FAILURE;
        }

        $contents = $filesystem->get($path);
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;

        if (!$dom->loadXML($contents)) {
            $this->error('Feed XML is not well-formed.');
            foreach (libxml_get_errors() as $error) {
                $this->line(sprintf('Line %d: %s', $error->line, trim($error->message)));
            }
            libxml_clear_errors();
            return self::FAILURE;
        }

        libxml_clear_errors();

        $rss = $dom->getElementsByTagName('rss')->item(0);
        if (!$rss) {
            $this->error('Root <rss> element not found.');
            return self::FAILURE;
        }

        if (!$rss->hasAttribute('xmlns:g')) {
            $this->error('Google namespace (xmlns:g) is missing from the root element.');
            return self::FAILURE;
        }

        $channel = $dom->getElementsByTagName('channel')->item(0);
        if (!$channel) {
            $this->error('Feed is missing <channel> element.');
            return self::FAILURE;
        }

        if ($channel->getElementsByTagName('item')->length === 0) {
            $this->warn('Feed validated but contains no <item> nodes.');
        }

        $this->info(sprintf('Feed %s on disk %s is well-formed and includes Google namespace.', $path, $disk));
        return self::SUCCESS;
    }
}

