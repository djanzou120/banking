<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => Role::ROOT, 'name' => 'ROOT', 'level' => 100],
            ['id' => Role::CASHIER, 'name' => 'CASHIER', 'level' => 70],
        ];

        Role::insert($data);
    }
}
