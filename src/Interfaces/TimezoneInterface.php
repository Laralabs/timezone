<?php

namespace Laralabs\Timezone\Interfaces;

use Laralabs\Timezone\TimezoneDate;

interface TimezoneInterface
{
    /**
     * Convert timestamp from display to storage timezone.
     *
     * @param null $date
     * @param null $fromTimezone
     *
     * @return TimezoneDate|null
     */
    public function convertToStorage($date = null, $fromTimezone = null): ?TimezoneDate;

    /**
     * Convert timestamp from storage to display timezone.
     *
     * @param null $date
     * @param null $toTimezone
     *
     * @return TimezoneDate|null
     */
    public function convertFromStorage($date = null, $toTimezone = null): ?TimezoneDate;

    /**
     * Converts timestamps or specified columns from storage
     * to display timezone.
     *
     * @param null|\Illuminate\Support\Collection $collection
     * @param array                               $columns
     * @param null|string                         $fromTimezone
     *
     * @return \Illuminate\Support\Collection
     */
    public function convertCollectionToStorage($collection = null, array $columns = [], $fromTimezone = null): \Illuminate\Support\Collection;

    /**
     * Converts timestamps or specified columns from storage
     * to display timezone.
     *
     * @param null|\Illuminate\Support\Collection $collection
     * @param array                               $columns
     * @param null|string                         $toTimezone
     *
     * @return \Illuminate\Support\Collection
     */
    public function convertCollectionFromStorage($collection = null, array $columns = [], $toTimezone = null): \Illuminate\Support\Collection;

    /**
     * Returns array of PHP timezones.
     *
     * @return array
     */
    public function getTimezones(): array;

    /**
     * Check if given date is a timestamp.
     *
     * @param $date
     *
     * @return bool
     */
    public function isTimestamp($date): bool;
}
