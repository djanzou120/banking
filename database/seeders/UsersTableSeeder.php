<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'firstname' => 'Banking',
                'lastname' => 'Root',
                'email' => 'root@banking.com',
                'password' => bcrypt('password'),
                'roleId' => Role::ROOT
            ],
            [
                'id' => 2,
                'firstname' => 'Banking',
                'lastname' => 'Cashier',
                'email' => 'cashier@banking.com',
                'password' => bcrypt('password'),
                'roleId' => Role::CASHIER
            ]
        ];

        User::insert($data);
    }
}
