<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brochure;

class BrochureSeeder extends Seeder
{
    public function run(): void
    {
        Brochure::factory(10)->create();
    }
}
