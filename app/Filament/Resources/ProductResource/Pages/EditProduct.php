<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Services\VariantGeneratorService;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateVariants')
                ->label('Varyant Oluştur')
                ->icon('heroicon-o-squares-plus')
                ->form(function (Get $get) {
                    $categoryIds = $this->record->categories->pluck('id')->toArray();
                    if (empty($categoryIds)) {
                        return [
                            Forms\Components\Placeholder::make('no_attributes')
                                ->label('Önce Kategori Seçin')
                                ->content('Varyant oluşturmak için lütfen önce ürünün kategorilerini seçin.')
                        ];
                    }
                    $attributes = \App\Models\ProductAttribute::whereHas('categories', function ($query) use ($categoryIds) {
                        $query->whereIn('categories.id', $categoryIds);
                    })->where('is_variant', true)->limit(10)->get();

                    if($attributes->isEmpty()) {
                        return [
                            Forms\Components\Placeholder::make('no_variant_attributes')
                                ->label('Varyant Özelliği Yok')
                                ->content('Seçilen kategorilere atanmış bir varyant özelliği bulunamadı.')
                        ];
                    }

                    return $attributes->map(function ($attribute) {
                        return Forms\Components\TagsInput::make('attributes.' . $attribute->id)
                            ->label($attribute->name);
                    })->all();
                })
                ->action(function (array $data) {
                    try {
                        $service = app(VariantGeneratorService::class);
                        $variants = $service->generateVariants($this->record, $data['attributes']);
                        Notification::make()
                            ->title($variants->count() . ' adet varyant başarıyla oluşturuldu.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Varyant oluşturma başarısız oldu!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
