<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SearchDbForString extends Command
{
    /**
     * Komut adı ve imzası.
     */
    protected $signature = 'app:search-db {needle : Aranacak metin} {--limit=5 : Her tabloda döndürülecek maksimum kayıt sayısı}';

    /**
     * Komut açıklaması.
     */
    protected $description = 'Tüm tabloların metin kolonlarında verilen metni arar ve eşleşmeleri listeler';

    public function handle(): int
    {
        // Girilen metin
        $needle = (string) $this->argument('needle');
        $limit = (int) $this->option('limit');

        if ($needle === '') {
            $this->error('needle boş olamaz');
            return self::FAILURE;
        }

        $databaseName = (string) DB::getDatabaseName();
        $this->info("Veritabanı: {$databaseName}");
        $this->info("Aranan: {$needle}");

        // Tabloları al
        $tables = DB::select(
            'SELECT table_name FROM information_schema.tables WHERE table_schema = ?',
            [$databaseName]
        );

        $foundAny = false;

        foreach ($tables as $tableRow) {
            $tableName = $tableRow->table_name ?? null;
            if ($tableName === null) {
                continue;
            }

            // Metin kolonlarını al
            $columns = DB::select(
                'SELECT column_name, data_type FROM information_schema.columns WHERE table_schema = ? AND table_name = ?',
                [$databaseName, $tableName]
            );

            $textColumns = array_values(array_filter($columns, function ($col): bool {
                $type = strtolower((string) ($col->data_type ?? ''));
                return in_array($type, ['varchar', 'text', 'longtext', 'mediumtext', 'char'], true);
            }));

            if (count($textColumns) === 0) {
                continue;
            }

            // LIKE koşulları
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $needle) . '%';
            $wheres = [];
            $bindings = [];
            foreach ($textColumns as $col) {
                $colName = (string) $col->column_name;
                $wheres[] = "`{$colName}` LIKE ?";
                $bindings[] = $like;
            }

            $sql = 'SELECT * FROM `' . $tableName . '` WHERE ' . implode(' OR ', $wheres) . ' LIMIT ' . $limit;

            try {
                $rows = DB::select($sql, $bindings);
            } catch (\Throwable $e) {
                // Tablo okunamıyorsa atla
                continue;
            }

            if (count($rows) > 0) {
                $foundAny = true;
                $this->line("\n[{$tableName}] ilk " . min($limit, count($rows)) . ' eşleşme:');
                foreach ($rows as $row) {
                    // Satırı JSON olarak yaz
                    $this->line(json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }
            }
        }

        if (! $foundAny) {
            $this->info('Herhangi bir eşleşme bulunamadı.');
        }

        return self::SUCCESS;
    }
}


