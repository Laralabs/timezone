<?php

namespace Laralabs\Timezone\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Laralabs\Timezone\Presenter\TimezonePresenter;
use Laralabs\Timezone\Tests\Model\TestModel;
use Laralabs\Timezone\Tests\Model\TestModelPresenter;
use Laralabs\Timezone\Timezone;

class TimezoneTest extends TestCase
{
    /** @test */
    public function timezone_function_returns_timezone_instance(): void
    {
        $timezone = timezone();
        $this->assertInstanceOf(Timezone::class, $timezone);
    }

    /** @test */
    public function helper_it_converts_timestamp_from_storage(): void
    {
        $converted = timezone()->fromStorage($this->testUTC);

        $this->assertEquals($this->testEuropeLondon, $converted);
    }

    /** @test */
    public function helper_it_converts_timestamp_to_storage(): void
    {
        $converted = timezone()->toStorage($this->testEuropeLondon);

        $this->assertEquals($this->testUTC, $converted);
    }

    /** @test */
    public function facade_it_converts_timestamp_from_storage(): void
    {
        $converted = \Laralabs\Timezone\Facades\Timezone::fromStorage($this->testUTC);

        $this->assertEquals($this->testEuropeLondon, $converted);
    }

    /** @test */
    public function facade_it_converts_timestamp_to_storage(): void
    {
        $converted = \Laralabs\Timezone\Facades\Timezone::toStorage($this->testEuropeLondon);

        $this->assertEquals($this->testUTC, $converted);
    }

    /** @test */
    public function helper_it_can_check_if_timestamp(): void
    {
        $this->assertFalse(timezone()->isTimestamp($this->testDate));
        $this->assertTrue(timezone()->isTimestamp($this->testUTC));
    }

    /** @test */
    public function facade_it_can_check_if_timestamp(): void
    {
        $this->assertFalse(\Laralabs\Timezone\Facades\Timezone::isTimestamp($this->testDate));
        $this->assertTrue(\Laralabs\Timezone\Facades\Timezone::isTimestamp($this->testUTC));
    }

    /** @test */
    public function it_can_format_to_locale(): void
    {
        $converted = \Laralabs\Timezone\Facades\Timezone::fromStorage($this->testUTC)->formatToLocale($this->testLocaleFormat, $this->testLocale);

        $this->assertEquals($this->testLocaleResult, $converted);
    }

    /** @test */
    public function it_caches_timezone_array(): void
    {
        $timezones = timezone()->getTimezones();

        $this->assertTrue(Cache::has('timezone.timezones'));

        $cached = \Laralabs\Timezone\Facades\Timezone::getTimezones();
        $this->assertEquals($timezones, $cached);
    }

    /** @test */
    public function it_parses_full_uk_date_to_storage_and_formats_it_back(): void
    {
        Config::set('timezone.parse_uk_dates', true);

        $converted = timezone()->toStorage($this->testUKParse);

        $this->assertEquals($this->testUTC, $converted);

        $convertedBack = timezone()->fromStorage($converted)->format($this->testUKFormat);

        $this->assertEquals($this->testUKParse, $convertedBack);
    }

    /** @test */
    public function it_accepts_unix_timestamp(): void
    {
        $converted = timezone()->fromStorage(strtotime($this->testUTC));

        $this->assertEquals($this->testEuropeLondon, $converted);
    }

    /** @test */
    public function it_accepts_date_and_keeps_utc_timezone(): void
    {
        Config::set('timezone.parse_uk_dates', true);
        $converted = timezone()->fromStorage($this->testDate);

        $this->assertEquals($this->testDate, $converted->format('d/m/Y'));
        $timezone = new \DateTimeZone('UTC');
        $this->assertEquals($timezone, $converted->timezone);
    }

    /** @test */
    public function it_accepts_carbon_instance(): void
    {
        $model = TestModel::first();

        $carbon = $model->timestamp;
        $this->assertInstanceOf(Carbon::class, $carbon);

        $converted = timezone()->fromStorage($carbon);
        $this->assertEquals($this->testEuropeLondon, $converted);
    }

    /** @test */
    public function trait_returns_instance_of_timezone_presenter(): void
    {
        $model = TestModelPresenter::first();

        $presenter = $model->timezone();

        $this->assertInstanceOf(TimezonePresenter::class, $presenter);
    }

    /** @test */
    public function trait_throws_exception_with_no_timezone_dates_array(): void
    {
        $model = TestModel::first();

        $this->expectExceptionMessage('Property $timezoneDates is not set correctly in '.\get_class($model));

        $model->timezone();
    }

    /** @test */
    public function presenter_get_returns_instance_of_presenter(): void
    {
        $model = TestModelPresenter::first();

        $this->assertInstanceOf(TimezonePresenter::class, $model->timezone()->datetime);
    }

    /** @test */
    public function presenter_get_throws_exception_when_property_not_defined_in_array(): void
    {
        $model = TestModelPresenter::first();

        $this->expectExceptionMessage('Property test not found in '.\get_class($model).'::$timezoneDates');

        $model->timezone()->test->display();
    }

    /** @test */
    public function presenter_set_throws_exception_when_property_not_defined_in_array(): void
    {
        $model = TestModelPresenter::first();

        $this->expectExceptionMessage('Property test not found in '.\get_class($model).'::$timezoneDates');

        $model->timezone()->test = $this->testUTC;
    }

    /** @test */
    public function presenter_throws_exception_when_no_property_specified(): void
    {
        $model = TestModelPresenter::first();

        $this->expectExceptionMessage('Please specify a property before attempting to convert it');

        $model->timezone()->display();
    }

    /** @test */
    public function presenter_it_displays_to_format_and_locale_specified_in_array_and_back(): void
    {
        $model = TestModelPresenter::first();

        $converted = $model->timezone()->timestamp->display();

        $this->assertEquals($this->testLocaleResult, $converted);

        App::setLocale($this->testLocale);

        $model->timezone()->timestamp = $converted;
        $this->assertEquals($this->testUTC, $model->timestamp);

        App::setLocale('en');
    }
}
