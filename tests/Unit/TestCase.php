<?php

namespace Tests\Unit;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * The setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }
}
