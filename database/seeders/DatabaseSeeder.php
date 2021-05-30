<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $category_seeder = new CategorySeeder();
        $user_seeder = new UserSeeder();

        $category_seeder->run();
        $user_seeder->run();
    }
}
