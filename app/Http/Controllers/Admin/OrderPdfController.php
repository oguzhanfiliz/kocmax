<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Pdf\OrderPdfService;

class OrderPdfController extends Controller
{
    public function download(Order $order, OrderPdfService $service)
    {
        $pdf = $service->render($order);
        $fileName = 'order-' . $order->order_number . '.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}

