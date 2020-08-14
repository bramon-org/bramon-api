<?php

use App\Models\Station;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

// @codingStandardsIgnoreLine
class CreateDefaultStation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $users = User::get();

        foreach ($users as $user) {
            foreach (Station::AVAILABLE_SOURCES as $source) {
                $station = new Station();
                $station->user_id = $user->id;
                $station->name = $faker->name;
                $station->latitude = $faker->latitude;
                $station->longitude = $faker->longitude;
                $station->azimuth = $faker->numberBetween(0, 360);
                $station->elevation = $faker->numberBetween(0, 90);
                $station->fov = $faker->numberBetween(0, 360);
                $station->camera_model = $faker->company;
                $station->camera_lens = $faker->company;
                $station->camera_capture = $faker->company;
                $station->source = $source;
                $station->save();

                dump([
                    'user' => $user->id,
                    'email' => $user->email,
                    'api_token' => $user->api_token,
                    'station' => $station->id,
                    'source' => $station->source,
                ]);
            }
        }
    }
}
