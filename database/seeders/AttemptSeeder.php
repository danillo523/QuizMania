<?php

namespace Database\Seeders;

use App\Models\Attempt;
use Illuminate\Database\Seeder;

class AttemptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Attempt::factory(10)->create();
    }
}
