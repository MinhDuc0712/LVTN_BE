<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
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

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        // $this->call([
        //     CategorySeeder::class,
        // ]);
        Role::create([
        'MaQuyen' => 1,
        'TenQuyen' => 'guest',
        'MoTaQuyen' => 'Người tìm kiếm phòng trọ'
    ]);
    
    Role::create([
        'MaQuyen' => 2,
        'TenQuyen' => 'owner',
        'MoTaQuyen' => 'Chủ nhà cho thuê phòng'
    ]);
    
    Role::create([
        'MaQuyen' => 3,
        'TenQuyen' => 'admin',
        'MoTaQuyen' => 'Quản trị hệ thống'
    ]);
    }
}
