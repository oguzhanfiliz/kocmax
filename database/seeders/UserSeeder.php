<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcısı
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'phone' => '+905551234567',
                'position' => 'Sistem Yöneticisi',
                'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'is_active' => true,
            ]
        );
        $admin->assignRole(['admin', 'super_admin']);

        // Editor kullanıcısı
        $editor = User::updateOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password'),
                'phone' => '+905551234568',
                'position' => 'İçerik Editörü',
                'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'is_active' => true,
            ]
        );
        $editor->assignRole('editor');

        // Author kullanıcısı
        $author = User::updateOrCreate(
            ['email' => 'author@example.com'],
            [
                'name' => 'Author User',
                'password' => Hash::make('password'),
                'phone' => '+905551234569',
                'position' => 'İçerik Yazarı',
                'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'is_active' => true,
            ]
        );
        $author->assignRole('author');
    }
}
