<?php

declare(strict_types=1);

namespace App\Filament\Filters;

use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ProductSortFilter extends Filter
{
    protected static string $view = 'filament.tables.filters.product-sort-filter';

    public function apply(Builder $query, array $data): Builder
    {
        $sortBy = $data['sort_by'] ?? 'sort_order';
        $direction = $data['sort_direction'] ?? 'asc';

        return match ($sortBy) {
            'name' => $query->orderBy('name', $direction),
            'price' => $query->orderBy('base_price', $direction),
            'created_at' => $query->orderBy('created_at', $direction),
            'sort_order' => $query->orderBy('sort_order', $direction),
            default => $query->orderBy('sort_order', 'asc'),
        };
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('sort_by')
                ->label('Sıralama Kriteri')
                ->options([
                    'sort_order' => 'Sıralama Sırası',
                    'name' => 'Ürün Adı',
                    'price' => 'Fiyat',
                    'created_at' => 'Oluşturulma Tarihi',
                ])
                ->default('sort_order')
                ->reactive(),

            Select::make('sort_direction')
                ->label('Sıralama Yönü')
                ->options([
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ])
                ->default('asc')
                ->visible(fn (callable $get) => $get('sort_by') !== null),
        ];
    }

    public function getLabel(): string
    {
        return 'Sıralama';
    }
}
