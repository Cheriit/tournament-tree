<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factory = new UserFactory();
        for ($i = 0; $i < 20; $i ++) {
            User::create($factory->definition());
        }
    }
}
