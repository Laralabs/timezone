<?php

namespace Laralabs\Timezone\Tests;

use Dotenv\Dotenv;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
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
    protected $testFormat = 'Y-m-d H:i:s';

    /** @var string */
    protected $testUTC = '2018-07-25 13:00:00';

    /** @var string */
    protected $testEuropeLondon = '2018-07-25 14:00:00';

    /** @var string */
    protected $testUKParse = '25/07/2018 14:00:00';

    /** @var string */
    protected $testUKFormat = 'd/m/Y H:i:s';

    /** @var string */
    protected $testDateUTC = '2018-07-25';

    /** @var string */
    protected $testDate = '25/07/2018';

    /** @var string */
    protected $testTimeUTC = '13:00:00';

    /** @var string */
    protected $testTime = '14:00:00';

    /** @var string */
    protected $testLocale = 'nl';

    /** @var string */
    protected $testLocaleFormat = 'l j F Y H:i:s';

    /** @var string */
    protected $testLocaleResult = 'woensdag 25 juli 2018 14:00:00';

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
            $table->date('date');
            $table->time('time');
        });

        TestModel::create([
            'name'      => 'Test Model',
            'timestamp' => $this->testUTC,
            'datetime'  => $this->testUTC,
            'date'      => $this->testDateUTC,
            'time'      => $this->testTimeUTC
        ]);

        TestModel::create([
            'name'      => 'Test Model 2',
            'timestamp' => $this->testUTC,
            'datetime'  => $this->testUTC,
            'date'      => $this->testDateUTC,
            'time'      => $this->testTimeUTC
        ]);
    }

    protected function getExpectedTestArray(): Collection
    {
        return collect([
            [
                'name'      => 'Test Model',
                'timestamp' => $this->testEuropeLondon,
                'datetime'  => $this->testEuropeLondon,
                'id'        => 1,
            ],
            [
                'name'      => 'Test Model 2',
                'timestamp' => $this->testEuropeLondon,
                'datetime'  => $this->testEuropeLondon,
                'id'        => 2,
            ],
        ]);
    }
}
