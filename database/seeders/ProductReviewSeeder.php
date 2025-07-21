<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_active', true)->take(5)->get();
        $products = Product::where('is_active', true)->take(5)->get();

        $comments = [
            'Çok kaliteli ve dayanıklı bir ürün, tavsiye ederim!',
            'Fiyat/performans açısından gayet iyi.',
            'Beklediğimden hızlı teslimat, teşekkürler.',
            'Ürün açıklamadaki gibi, memnun kaldım.',
            'Kargo biraz gecikti ama ürün güzel.',
            'Kalıbı biraz dar, bir beden büyük alınabilir.',
            'Renk canlı ve kumaşı kaliteli.',
            'Kullanımı rahat, iş güvenliği için ideal.',
            'Paketleme özenliydi.',
            'Fiyatı biraz yüksek ama değer.'
        ];

        foreach ($products as $product) {
            foreach ($users as $i => $user) {
                ProductReview::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'rating' => rand(3, 5),
                        'title' => 'Ürün Yorumu #' . ($i + 1),
                        'comment' => $comments[array_rand($comments)],
                        'is_approved' => $i % 2 === 0,
                        'is_verified_purchase' => $i % 3 === 0,
                    ]
                );
            }
        }
    }
} 