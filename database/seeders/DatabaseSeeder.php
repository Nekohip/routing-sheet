<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProcessType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Managers
        User::create([
            'username' => 'admin1',
            'name' => '張主管',
            'role' => 'manager',
            'password' => Hash::make('admin1'),
        ]);

        User::create([
            'username' => 'admin2',
            'name' => '李主管',
            'role' => 'manager',
            'password' => Hash::make('admin2'),
        ]);

        // Workers
        User::create([
            'username' => 'worker1',
            'name' => '王小明',
            'role' => 'worker',
            'password' => Hash::make('worker1'),
        ]);

        User::create([
            'username' => 'worker2',
            'name' => '陳大華',
            'role' => 'worker',
            'password' => Hash::make('worker2'),
        ]);

        // Process Types
        $processTypes = ['生產', '分尺寸', '回火', '研磨', '精研', '珠擊', '預壓'];
        foreach ($processTypes as $name) {
            ProcessType::create([
                'name' => $name,
                'is_default' => true,
            ]);
        }
    }
}
