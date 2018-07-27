<?php

namespace Laralabs\Timezone\Facades;

use Illuminate\Support\Collection;
use Laralabs\Timezone\Interfaces\TimezoneInterface;
use Laralabs\Timezone\TimezoneDate;

class TimezoneFacade implements TimezoneInterface
{
    /**
     * Convert timestamp from display to storage timezone.
     *
     * @param null $date
     * @param null $fromTimezone
     *
     * @return TimezoneDate|null
     */
    public function convertToStorage($date = null, $fromTimezone = null): ?TimezoneDate
    {
        return app('timezone')->convertToStorage($date, $fromTimezone);
    }

    /**
     * Convert timestamp from storage to display timezone.
     *
     * @param null $date
     * @param null $toTimezone
     *
     * @return TimezoneDate|null
     */
    public function convertFromStorage($date = null, $toTimezone = null): ?TimezoneDate
    {
        return app('timezone')->convertFromStorage($date, $toTimezone);
    }

    /**
     * Converts timestamps or specified columns to storage
     * from display timezone.
     *
     * @param null|\Illuminate\Support\Collection $collection
     * @param array                               $columns
     * @param null|string|array                   $format
     * @param null|string                         $fromTimezone
     *
     * @return \Illuminate\Support\Collection
     */
    public function convertCollectionToStorage($collection = null, array $columns = [], $format = null, $fromTimezone = null): Collection
    {
        return app('timezone')->convertCollectionToStorage($collection, $columns, $format, $fromTimezone);
    }

    /**
     * Converts timestamps or specified columns from storage
     * to display timezone.
     *
     * @param null|\Illuminate\Support\Collection $collection
     * @param array                               $columns
     * @param null|string|array                   $format
     * @param null|string                         $toTimezone
     *
     * @return \Illuminate\Support\Collection
     */
    public function convertCollectionFromStorage($collection = null, array $columns = [], $format = null, $toTimezone = null): Collection
    {
        return app('timezone')->convertCollectionFromStorage($collection, $columns, $format, $toTimezone);
    }

    /**
     * Returns array of PHP timezones.
     *
     * @return array
     */
    public function getTimezones(): array
    {
        return app('timezone')->getTimezones();
    }
}
