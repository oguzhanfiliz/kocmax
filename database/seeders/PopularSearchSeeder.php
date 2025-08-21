<?php

namespace Database\Seeders;

use App\Models\PopularSearch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PopularSearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $searches = [
            ['query' => 'güvenlik ayakkabısı', 'count' => 150],
            ['query' => 'iş eldiveni', 'count' => 120],
            ['query' => 'baret', 'count' => 110],
            ['query' => 'reflektör yelek', 'count' => 95],
            ['query' => 'çelik burun', 'count' => 85],
            ['query' => 'hafif', 'count' => 75],
            ['query' => 'güvenlik', 'count' => 70],
            ['query' => 'iş ayakkabısı', 'count' => 65],
            ['query' => 'koruyucu gözlük', 'count' => 60],
            ['query' => 'anti statik', 'count' => 55],
            ['query' => 'su geçirmez', 'count' => 50],
            ['query' => 'solunum maskesi', 'count' => 45],
            ['query' => 'işçi tulumu', 'count' => 40],
            ['query' => 'kemik eldiven', 'count' => 35],
            ['query' => 'kaygan olmayan', 'count' => 30],
            ['query' => 'kompozit burun', 'count' => 28],
            ['query' => 'kış ayakkabısı', 'count' => 25],
            ['query' => 'yansıtıcı bant', 'count' => 22],
            ['query' => 'çok amaçlı', 'count' => 20],
            ['query' => 'nefes alan', 'count' => 18],
            ['query' => 'darbeye dayanıklı', 'count' => 15],
            ['query' => 'ergonomik', 'count' => 12],
            ['query' => 'ağır hizmet', 'count' => 10],
            ['query' => 'profesyonel', 'count' => 8],
            ['query' => 'yüksek kalite', 'count' => 5],
        ];

        foreach ($searches as $search) {
            PopularSearch::updateOrCreate(
                ['query' => $search['query']],
                ['count' => $search['count']]
            );
        }
    }
}