<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => Status::INIT, 'name' => 'INIT'],
            ['id' => Status::PENDING, 'name' => 'PENDING'],
            ['id' => Status::FAILED, 'name' => 'FAILED'],
            ['id' => Status::ABORT, 'name' => 'ABORT'],
            ['id' => Status::SUCCESS, 'name' => 'SUCCESS'],
        ];

        Status::insert($data);
    }
}
