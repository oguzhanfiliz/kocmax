<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Enums\OrderStatus;
use App\Services\Order\OrderService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('PDF İndir')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('gray')
                ->url(fn() => route('admin.orders.pdf', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('approve')
                ->label('Onayla (İşleme Al)')
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->visible(fn() => in_array($this->record->status, ['pending']) && (auth()->user()?->hasRole(['admin','manager']) ?? false))
                ->form([
                    Forms\Components\Textarea::make('notes')
                        ->label('Not')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    app(OrderService::class)->updateStatus($this->record, OrderStatus::Processing, auth()->user(), $data['notes'] ?? null);
                    Notification::make()->title('Sipariş işleme alındı')->success()->send();
                }),

            Actions\Action::make('ship')
                ->label('Kargoya Ver')
                ->icon('heroicon-m-truck')
                ->color('primary')
                ->visible(fn() => in_array($this->record->status, ['processing']) && (auth()->user()?->hasRole(['admin','manager']) ?? false))
                ->form([
                    Forms\Components\TextInput::make('tracking_number')->label('Takip Numarası')->required(),
                    Forms\Components\TextInput::make('shipping_carrier')->label('Kargo Firması')->required(),
                ])
                ->action(function (array $data) {
                    app(OrderService::class)->markAsShipped($this->record, $data['tracking_number'], $data['shipping_carrier'], auth()->user());
                    Notification::make()->title('Sipariş kargoya verildi')->success()->send();
                }),

            Actions\Action::make('deliver')
                ->label('Teslim Edildi')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(fn() => in_array($this->record->status, ['shipped']) && (auth()->user()?->hasRole(['admin','manager']) ?? false))
                ->requiresConfirmation()
                ->action(function () {
                    app(OrderService::class)->markAsDelivered($this->record, auth()->user());
                    Notification::make()->title('Sipariş teslim edildi')->success()->send();
                }),

            Actions\Action::make('cancel')
                ->label('İptal Et')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn() => in_array($this->record->status, ['pending','processing']) && (auth()->user()?->hasRole(['admin','manager']) ?? false))
                ->form([
                    Forms\Components\Textarea::make('reason')->label('İptal Nedeni')->required()->rows(2)->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    app(OrderService::class)->cancelOrder($this->record, auth()->user(), $data['reason']);
                    Notification::make()->title('Sipariş iptal edildi')->success()->send();
                }),

            Actions\EditAction::make()->label('Düzenle'),
        ];
    }

    public function getTitle(): string
    {
        return 'Sipariş: ' . $this->record->order_number;
    }
}
