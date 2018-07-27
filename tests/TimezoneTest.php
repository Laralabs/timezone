<?php

namespace Laralabs\Timezone\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Laralabs\Timezone\Tests\Model\TestModel;
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
    public function it_can_format_to_locale(): void
    {
        $converted = \Laralabs\Timezone\Facades\Timezone::convertFromStorage($this->testUTC)->formatToLocale('l j F Y H:i:s', 'nl');
        $intended = 'woensdag 25 juli 2018 14:00:00';

        $this->assertEquals($intended, $converted);
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
        
        $convertedBack = timezone()->convertFromStorage($converted)->format('d/m/Y H:i:s');

        $this->assertEquals($this->testUKParse, $convertedBack);
    }
}
