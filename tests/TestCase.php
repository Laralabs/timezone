<?php

namespace Laralabs\Timezone\Tests;

use Dotenv\Dotenv;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Jenssegers\Date\Date;
use Laralabs\Timezone\Tests\Model\TestModel;
use Laralabs\Timezone\TimezoneServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /** @var \Laralabs\Timezone\Tests\Model\TestModel */
    protected $testModel;

    /** @var array */
    protected $testColumns = ['datetime'];

    /** @var string */
    protected $testUTC = '2018-07-25 13:00:00';

    /** @var string */
    protected $testEuropeLondon = '2018-07-25 14:00:00';

    public function setUp(): void
    {
        $this->loadEnvironmentVariables();

        parent::setUp();

        $this->setUpEnvironment($this->app);
        $this->setUpDatabase($this->app);

        Config::set('timezone.timezone', 'Europe/London');

        $this->testModel = TestModel::first();

        date_default_timezone_set('UTC');
        Date::setLocale('en');
    }

    protected function loadEnvironmentVariables(): void
    {
        if (!file_exists(__DIR__.'/../.env')) {
            return;
        }

        $dotenv = new Dotenv(__DIR__.'/..');

        $dotenv->load();
    }

    protected function getPackageProviders($app): array
    {
        return [
            TimezoneServiceProvider::class,
        ];
    }

    protected function setUpEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('app.key', 'XUec80T87lfeD41mWUaji7JJlEv7CEhSXcvtHkLu3Nw=');
    }

    protected function setUpDatabase($app): void
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamp('timestamp');
            $table->dateTime('datetime');
        });

        TestModel::create([
            'name'      => 'Test Model',
            'timestamp' => $this->testUTC,
            'datetime'  => $this->testUTC,
        ]);

        TestModel::create([
            'name'      => 'Test Model 2',
            'timestamp' => $this->testUTC,
            'datetime'  => $this->testUTC,
        ]);
    }
}
