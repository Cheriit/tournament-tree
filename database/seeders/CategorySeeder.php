<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'League of legends',
            'Counterstrike Global Offensive',
            'StarCraft 2',
            'Dota 2',
            'World of Warcraft'
        ];
        foreach ($categories as $category) {
            Category::create([
                'name' => $category
            ]);
        }
    }
}
