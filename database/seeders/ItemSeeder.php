<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->insert([
            [
                'name' => 'hair cut',
                'memo' => 'details',
                'price' => 6000
            ],
            [
                'name' => 'hair color',
                'memo' => 'details',
                'price' => 8000
            ],
            [
                'name' => 'hair permanent',
                'memo' => 'details',
                'price' => 10000
            ],
        ]);
    }
}
