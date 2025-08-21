<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Test için kullanıcı oluştur
        User::factory()->create(['id' => 1]);
    }

    public function test_boolean_values_are_handled_correctly(): void
    {
        // Boolean ayar oluştur
        $setting = Setting::create([
            'key' => 'test_boolean',
            'value' => '1',
            'type' => 'boolean',
            'group' => 'test',
            'label' => 'Test Boolean',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        // Boolean değerin doğru şekilde okunduğunu kontrol et
        $this->assertTrue($setting->value);

        // String değer oluştur
        $stringSetting = Setting::create([
            'key' => 'test_string',
            'value' => 'test string value',
            'type' => 'string',
            'group' => 'test',
            'label' => 'Test String',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        // String değerin doğru şekilde okunduğunu kontrol et
        $this->assertEquals('test string value', $stringSetting->value);
    }

    public function test_boolean_setting_with_string_value_returns_false(): void
    {
        // Boolean tipinde ama string değer içeren ayar
        $setting = Setting::create([
            'key' => 'test_boolean_string',
            'value' => 'some random text',
            'type' => 'boolean',
            'group' => 'test',
            'label' => 'Test Boolean String',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        // String değer boolean olarak false dönmeli
        $this->assertFalse($setting->value);
    }

    public function test_string_setting_with_boolean_like_value_returns_string(): void
    {
        // String tipinde ama boolean benzeri değer içeren ayar
        $setting = Setting::create([
            'key' => 'test_string_boolean',
            'value' => 'true',
            'type' => 'string',
            'group' => 'test',
            'label' => 'Test String Boolean',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        // String değer string olarak kalmalı
        $this->assertEquals('true', $setting->value);
        $this->assertIsString($setting->value);
    }

    public function test_boolean_setting_saves_correctly(): void
    {
        $setting = new Setting([
            'key' => 'test_boolean_save',
            'type' => 'boolean',
            'group' => 'test',
            'label' => 'Test Boolean Save',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        // Boolean true değeri set et
        $setting->value = true;
        $setting->save();

        // Veritabanında '1' olarak kaydedildiğini kontrol et
        $this->assertDatabaseHas('settings', [
            'key' => 'test_boolean_save',
            'value' => '1',
        ]);

        // Model üzerinden okunduğunda true döndüğünü kontrol et
        $setting->refresh();
        $this->assertTrue($setting->value);
    }

    public function test_string_setting_saves_correctly(): void
    {
        $setting = new Setting([
            'key' => 'test_string_save',
            'type' => 'string',
            'group' => 'test',
            'label' => 'Test String Save',
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        // String değer set et
        $setting->value = 'test value';
        $setting->save();

        // Veritabanında string olarak kaydedildiğini kontrol et
        $this->assertDatabaseHas('settings', [
            'key' => 'test_string_save',
            'value' => 'test value',
        ]);

        // Model üzerinden okunduğunda string döndüğünü kontrol et
        $setting->refresh();
        $this->assertEquals('test value', $setting->value);
        $this->assertIsString($setting->value);
    }
}
