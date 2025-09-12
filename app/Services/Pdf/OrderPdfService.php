<?php

declare(strict_types=1);

namespace App\Services\Pdf;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPdfService
{
    public function render(Order $order): string
    {
        $order->loadMissing(['items.product', 'items.productVariant', 'user']);

        $pdf = Pdf::loadView('pdf.order', [
            'order' => $order,
        ])->setPaper('a4');

        return $pdf->output();
    }
}

