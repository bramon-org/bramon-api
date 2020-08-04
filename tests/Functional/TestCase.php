<?php

namespace Tests\Functional;

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
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        $this->faker = \Faker\Factory::create();

        return require __DIR__ . '/../../bootstrap/app.php';
    }

    /**
     * Generate an user to test
     *
     * @param string $role
     * @return User
     * @throws Exception
     */
    public function authenticate(string $role = User::ROLE_ADMIN): User
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
}
