<?php

namespace Tests\Functional;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        $this->faker = \Faker\Factory::create();

        return require __DIR__ . '/../../bootstrap/app.php';
    }
}
