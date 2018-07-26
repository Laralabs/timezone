<?php

namespace Laralabs\Timezone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Laralabs\Timezone\Interfaces\TimezoneInterface;

class Timezone implements TimezoneInterface
{
    /**
     * @var \Laralabs\Timezone\TimezoneDate
     */
    protected $date;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $storageTimezone;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $displayTimezone;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $parseUK;

    public function __construct()
    {
        $this->date = new TimezoneDate();
        $this->storageTimezone = config('app.timezone');
        $this->displayTimezone = config('timezone.timezone');
        $this->parseUK = config('timezone.parse_uk_dates') || env('TIMEZONE_UK', false);
    }

    /**
     * Convert timestamp from display to storage timezone.
     *
     * @param null $date
     * @param null $fromTimezone
     * @return TimezoneDate|null
     */
    public function convertToStorage($date = null, $fromTimezone = null): ?TimezoneDate
    {
        if (!$fromTimezone) { $fromTimezone = $this->displayTimezone; }
        $date = $this->createDate($date , $fromTimezone);

        $date->timezone = $this->storageTimezone;

        return $date;
    }

    /**
     * Convert timestamp from storage to display timezone.
     *
     * @param null $date
     * @param null $toTimezone
     * @return TimezoneDate|null
     */
    public function convertFromStorage($date = null, $toTimezone = null): ?TimezoneDate
    {
        if (!$toTimezone) { $toTimezone = $this->displayTimezone; }
        if (!\is_int($date) && !$this->isTimestamp($date)) { $toTimezone = $this->storageTimezone; }
        $date = $this->createDate($date);

        $date->timezone = $toTimezone;

        return $date;
    }

    /**
     * Converts timestamps or specified columns from storage
     * to display timezone.
     *
     * @param null|\Illuminate\Support\Collection $collection
     * @param array $columns
     * @param null|string $fromTimezone
     *
     * @return \Illuminate\Support\Collection
     */
    public function convertCollectionToStorage($collection = null, array $columns = [], $fromTimezone = null): Collection
    {
        if ($collection instanceof Collection) {
            if (!$fromTimezone) { $fromTimezone = $this->displayTimezone; }
            $params = ['columns' => $columns, 'fromTimezone' => $fromTimezone];

            return $collection->map(function ($item) use ($params) {
                if ($item instanceof Model) {
                    $params['columns'] = array_merge($params['columns'], $item->getDates());
                }
                foreach ($params['columns'] as $column) {
                    if (\is_array($item)) {
                        $item[$column] = $this->convertToStorage($item[$column], $params['fromTimezone']);
                    } else {
                        $item->$column = $this->convertToStorage($item->$column, $params['fromTimezone']);
                    }
                }
            });
        }

        throw new \InvalidArgumentException('A valid collection must be specified.');
    }

    /**
     * Converts timestamps or specified columns from storage
     * to display timezone.
     *
     * @param null|\Illuminate\Support\Collection $collection
     * @param array $columns
     * @param null|string $toTimezone
     *
     * @return \Illuminate\Support\Collection
     */
    public function convertCollectionFromStorage($collection = null, array $columns = [], $toTimezone = null): Collection
    {
        if ($collection instanceof Collection) {
            if (!$toTimezone) { $toTimezone = $this->displayTimezone; }
            $params = ['columns' => $columns, 'toTimezone' => $toTimezone];

            return $collection->map(function ($item) use ($params) {
                if ($item instanceof Model) {
                    $params['columns'] = array_merge($params['columns'], $item->getDates());
                }
                foreach ($params['columns'] as $column) {
                    if (\is_array($item)) {
                        $item[$column] = $this->convertFromStorage($item[$column], $params['toTimezone']);
                    } else {
                        $item->$column = $this->convertFromStorage($item->$column, $params['toTimezone']);
                    }
                }
            });
        }

        throw new \InvalidArgumentException('A valid collection must be specified.');
    }

    /**
     * Create a Date object.
     *
     * @param null $date
     * @param null $timezone
     * @return TimezoneDate
     */
    protected function createDate($date = null, $timezone = null): TimezoneDate
    {
        if (!$timezone) { $timezone = $this->storageTimezone; }
        if (\is_int($date)) { $date = date('Y-m-d H:i:s'); }
        if ($this->parseUK) { $date = $this->formatUKDate($date); }
        if (!$this->isTimestamp($date)) { $timezone = $this->storageTimezone; }
        return new $this->date($date, $timezone);
    }

    /**
     * Returns array of PHP timezones.
     *
     * @return array
     */
    public function getTimezones(): array
    {
        if (!Cache::has('timezone.timezones')) {
            static $timezones = null;

            if ($timezones === null) {
                $timezones = [];
                $offsets = [];
                $now = new \DateTime('now', new \DateTimeZone('UTC'));

                foreach (\DateTimeZone::listIdentifiers() as $timezone) {
                    $now->setTimezone(new \DateTimeZone($timezone));
                    $offsets[] = $offset = $now->getOffset();
                    $timezones[$timezone] = '(' . $this->formatGmtOffset($offset) . ') ' . $this->formatTimezoneName($timezone);
                }

                array_multisort($offsets, $timezones);
            }

            Cache::put('timezone.timezones', $timezones, 360);

            return $timezones;
        }

        return Cache::get('timezone.timezones');
    }

    /**
     * Format GMT offset.
     *
     * @param $offset
     * @return string
     */
    protected function formatGmtOffset($offset): string
    {
        $hours = (int)($offset / 3600);
        $minutes = abs((int)($offset % 3600 / 60));
        return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    /**
     * Format timezone name.
     *
     * @param $name
     * @return mixed
     */
    protected function formatTimezoneName($name): string
    {
        return str_replace(['/', '_', 'St '], [', ', ' ', 'St. '], $name);
    }

    /**
     * Format UK Date, replace '/' with '-'.
     *
     * @param $date
     * @return string
     */
    protected function formatUKDate($date): string
    {
        return str_replace('/', '-', $date);
    }

    /**
     * Check if given date is a timestamp.
     *
     * @param $date
     * @return bool
     */
    protected function isTimestamp($date): bool
    {
        return strpos($date, ':') ? true : false;
    }
}