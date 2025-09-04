<?php

declare(strict_types=1);

namespace App\Exceptions\Order;

use Exception;

/**
 * Yetersiz stok durumunda fırlatılan exception
 */
class InsufficientStockException extends Exception
{
    /**
     * Yetersiz stok exception'ı oluştur
     */
    public function __construct(string $message = 'Yetersiz stok', int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Exception'ı HTTP response'a çevir
     */
    public function render()
    {
        return response()->json([
            'error' => 'insufficient_stock',
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}