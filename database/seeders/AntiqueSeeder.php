<?php

namespace Database\Seeders;
use App\Models\User;

use App\Models\Antique;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory;

class AntiqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all()->pluck('id')->toArray();
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $antique = new Antique;
            $antique->name = $faker->sentence(3);
            $antique->description = $faker->text(200);
            $antique->price = $faker->randomFloat(2, 0, 10000);
            $antique->image = $faker->imageUrl(); // This creates external URLs
            $antique->user_id = $faker->randomElement($users);
            $antique->save();

        }
    }
}
