<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(AccessorySeeder::class);
        $this->call(ClothSeeder::class);

        $this->call(SchoolYearSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(UserSeeder::class);
    }
}
