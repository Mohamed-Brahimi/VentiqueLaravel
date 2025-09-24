<?php

namespace Database\Seeders;
use App\Models\Antique;
use App\Models\User;

use App\Models\Offer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Faker\Factory;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all()->pluck('id')->toArray();
        $antique = Antique::all()->pluck('id')->toArray();
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $offer = new Offer();
            $offer->price = $faker->randomFloat(2, 0, 10000);
            $offer->dateOffered = $faker->dateTimeThisDecade();
            $offer->erased = 0;
            $offer->antique_id = $faker->randomElement($antique);
            $offer->user_id = $faker->randomElement($users);
            $offer->save();

        }
    }
}
