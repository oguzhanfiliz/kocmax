<?php

namespace Database\Seeders;

use App\Models\AttributeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => AttributeType::TYPE_TEXT,
                'display_name' => 'Metin',
                'component' => 'TextInput',
                'config' => [],
            ],
            [
                'name' => AttributeType::TYPE_SELECT,
                'display_name' => 'Seçim Listesi',
                'component' => 'Select',
                'config' => ['multiple' => false],
            ],
            [
                'name' => AttributeType::TYPE_CHECKBOX,
                'display_name' => 'Çoklu Seçim',
                'component' => 'CheckboxList',
                'config' => ['inline' => true],
            ],
            [
                'name' => AttributeType::TYPE_RADIO,
                'display_name' => 'Tekli Seçim',
                'component' => 'Radio',
                'config' => ['inline' => true],
            ],
            [
                'name' => AttributeType::TYPE_COLOR,
                'display_name' => 'Renk Seçici',
                'component' => 'ColorPicker',
                'config' => [],
            ],
            [
                'name' => AttributeType::TYPE_NUMBER,
                'display_name' => 'Sayı',
                'component' => 'TextInput',
                'config' => ['numeric' => true],
            ],
            [
                'name' => AttributeType::TYPE_DATE,
                'display_name' => 'Tarih',
                'component' => 'DatePicker',
                'config' => [],
            ],
        ];

        foreach ($types as $type) {
            AttributeType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
