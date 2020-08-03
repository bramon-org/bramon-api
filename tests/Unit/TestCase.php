<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * The setup
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
    }
}
