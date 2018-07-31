<?php

namespace Laralabs\Timezone\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Jenssegers\Date\Date;
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
        $converted = timezone()->convertFromStorage($this->testUTC);

        $this->assertEquals($this->testEuropeLondon, $converted);
    }

    /** @test */
    public function helper_it_converts_timestamp_to_storage(): void
    {
        $converted = timezone()->convertToStorage($this->testEuropeLondon);

        $this->assertEquals($this->testUTC, $converted);
    }

    /** @test */
    public function facade_it_converts_timestamp_from_storage(): void
    {
        $converted = \Laralabs\Timezone\Facades\Timezone::convertFromStorage($this->testUTC);

        $this->assertEquals($this->testEuropeLondon, $converted);
    }

    /** @test */
    public function facade_it_converts_timestamp_to_storage(): void
    {
        $converted = \Laralabs\Timezone\Facades\Timezone::convertToStorage($this->testEuropeLondon);

        $this->assertEquals($this->testUTC, $converted);
    }

    /** @test */
    public function helper_it_converts_collection_from_storage_and_back(): void
    {
        $collection = TestModel::all();

        $this->assertInstanceOf(Collection::class, $collection);

        $converted = timezone()->convertCollectionFromStorage($collection, $this->testColumns);

        for ($i = 0; $i < $converted->count(); $i++) {
            $this->assertTrue($converted->contains('timestamp', $this->testEuropeLondon));
            $this->assertTrue($converted->contains('datetime', $this->testEuropeLondon));
        }

        $converted = timezone()->convertCollectionToStorage($converted, $this->testColumns);

        for ($i = 0; $i < $converted->count(); $i++) {
            $this->assertTrue($converted->contains('timestamp', $this->testUTC));
            $this->assertTrue($converted->contains('datetime', $this->testUTC));
        }
    }

    /** @test */
    public function facade_it_converts_collection_from_storage_and_back(): void
    {
        $collection = TestModel::all();

        $this->assertInstanceOf(Collection::class, $collection);

        $converted = \Laralabs\Timezone\Facades\Timezone::convertCollectionFromStorage($collection, $this->testColumns);

        for ($i = 0; $i < $converted->count(); $i++) {
            $this->assertTrue($converted->contains('timestamp', $this->testEuropeLondon));
            $this->assertTrue($converted->contains('datetime', $this->testEuropeLondon));
        }

        $converted = \Laralabs\Timezone\Facades\Timezone::convertCollectionToStorage($converted, $this->testColumns);

        for ($i = 0; $i < $converted->count(); $i++) {
            $this->assertTrue($converted->contains('timestamp', $this->testUTC));
            $this->assertTrue($converted->contains('datetime', $this->testUTC));
        }
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
        $converted = \Laralabs\Timezone\Facades\Timezone::convertFromStorage($this->testUTC)->formatToLocale($this->testLocaleFormat, $this->testLocale);

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

        $converted = timezone()->convertToStorage($this->testUKParse);

        $this->assertEquals($this->testUTC, $converted);

        $convertedBack = timezone()->convertFromStorage($converted)->format($this->testUKFormat);

        $this->assertEquals($this->testUKParse, $convertedBack);
    }

    /** @test */
    public function it_converts_collection_from_storage_and_back_with_string_format(): void
    {
        Config::set('timezone.parse_uk_dates', true);
        $collection = TestModel::all();

        $this->assertInstanceOf(Collection::class, $collection);

        $converted = timezone()->convertCollectionFromStorage($collection, $this->testColumns, $this->testUKFormat);

        for ($i = 0; $i < $converted->count(); $i++) {
            $this->assertTrue($converted->contains('timestamp', $this->testEuropeLondon));
            $this->assertTrue($converted->contains('datetime', $this->testUKParse));
        }

        $converted = timezone()->convertCollectionToStorage($converted, $this->testColumns);

        for ($i = 0; $i < $converted->count(); $i++) {
            $this->assertTrue($converted->contains('timestamp', $this->testUTC));
            $this->assertTrue($converted->contains('datetime', $this->testUTC));
        }
    }

    /** @test */
    public function it_converts_collection_from_storage_and_back_with_array_locale_format(): void
    {
        Config::set('timezone.parse_uk_dates', true);
        Date::setLocale($this->testLocale);
        $collection = TestModel::all();

        $this->assertInstanceOf(Collection::class, $collection);

        $converted = timezone()->convertCollectionFromStorage($collection, $this->testColumns, [$this->testLocaleFormat, $this->testLocale]);

        for ($i = 0; $i < $converted->count(); $i++) {
            $this->assertTrue($converted->contains('timestamp', $this->testEuropeLondon));
            $this->assertTrue($converted->contains('datetime', $this->testLocaleResult));
        }

        $converted = timezone()->convertCollectionToStorage($converted, $this->testColumns);

        for ($i = 0; $i < $converted->count(); $i++) {
            $this->assertTrue($converted->contains('timestamp', $this->testUTC));
            $this->assertTrue($converted->contains('datetime', $this->testUTC));
        }

        Date::setLocale('en');
    }

    /** @test */
    public function it_throws_exception_when_no_collection_passed(): void
    {
        $this->expectExceptionMessage('A valid collection must be specified.');

        timezone()->convertCollectionFromStorage([]);
    }
    
    /** @test */
    public function it_throws_exception_when_format_array_invalid(): void
    {
        $this->expectExceptionMessage('Argument 3 $format should contain format and locale when specified as an array.');
        
        timezone()->convertCollectionFromStorage(TestModel::all(), $this->testColumns, [$this->testFormat]);
    }
    
    /** @test */
    public function it_converts_collection_of_arrays(): void
    {
        $collection = TestModel::all()->toArray();

        $converted = timezone()->convertCollectionFromStorage(collect($collection), ['datetime', 'timestamp']);

        $intended = $this->getExpectedTestArray();

        $this->assertEquals($intended, $converted);
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
}
