<?php

namespace Tests\Functional;

use App\Models\Station;
use App\Models\User;
use Exception;
use Faker\Generator;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /**
     * @var Generator
     */
    protected Generator $faker;

    /**
     * @var User
     */
    protected User $user;

    /**
     * @var Station
     */
    protected Station $station;

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        $this->faker = \Faker\Factory::create();

        $app = require __DIR__ . '/../../bootstrap/app.php';

        config(['database.default' => 'testing']);

        return $app;
    }

    /**
     * Generate an user to test
     *
     * @param string $role
     * @return array
     * @throws Exception
     */
    public function authenticate(string $role = User::ROLE_ADMIN): array
    {
        return [
            $this->createDefaultUser($role),
            $this->createDefaultStation(),
        ];
    }

    /**
     * @param string $role
     * @return User
     * @throws Exception
     */
    private function createDefaultUser(string $role = User::ROLE_ADMIN): User
    {
        $this->user = new User();
        $this->user->email = $this->faker->email;
        $this->user->name = $this->faker->name;
        $this->user->mobile_phone = $this->faker->phoneNumber;
        $this->user->city = $this->faker->city;
        $this->user->state = $this->faker->state;
        $this->user->password = $this->user->generatePassword();
        $this->user->api_token = $this->user->generateApiToken();
        $this->user->role = $role;
        $this->user->save();

        return $this->user;
    }

    /**
     * @return Station
     */
    private function createDefaultStation(): Station
    {
        $this->station = new Station();
        $this->station->user_id = $this->user->id;
        $this->station->name = $this->faker->name;
        $this->station->latitude = $this->faker->latitude;
        $this->station->longitude = $this->faker->longitude;
        $this->station->azimuth = $this->faker->numberBetween(0, 360);
        $this->station->elevation = $this->faker->numberBetween(0, 90);
        $this->station->fov = $this->faker->numberBetween(0, 360);
        $this->station->camera_model = $this->faker->company;
        $this->station->camera_lens = $this->faker->company;
        $this->station->camera_capture = $this->faker->company;
        $this->station->source = Station::SOURCE_UFO;
        $this->station->save();

        return $this->station;
    }
}
