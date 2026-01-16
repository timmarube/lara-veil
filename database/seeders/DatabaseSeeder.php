<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@lara-veil.com',
            'password' => 'password'
        ]);

        // Activate default theme
        \App\Models\Theme::updateOrCreate(['slug' => 'default'], ['name' => 'Default Theme', 'is_active' => true]);
        
        // Activate sample plugin
        \App\Models\Plugin::updateOrCreate(['name' => 'hello-world'], [
            'namespace' => 'Vendor\\HelloWorld\\',
            'version' => '1.0.0',
            'status' => 'active'
        ]);
    }
}
