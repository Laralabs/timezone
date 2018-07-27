<?php

namespace Laralabs\Timezone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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
    protected $defaultFormat;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $parseUK;

    public function __construct()
    {
        $this->date = new TimezoneDate();
        $this->storageTimezone = config('app.timezone');
        $this->displayTimezone = config('timezone.timezone');
        $this->defaultFormat = config('timezone.format');
        $this->parseUK = config('timezone.parse_uk_dates') || env('TIMEZONE_UK', false);
    }

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
        if (!$fromTimezone) {
            $fromTimezone = $this->displayTimezone;
        }
        $date = $this->createDate($date, $fromTimezone);

        $date->timezone = $this->storageTimezone;

        return $date;
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
        if (!$toTimezone) {
            $toTimezone = $this->displayTimezone;
        }
        if (!\is_int($date) && !$this->isTimestamp($date)) {
            $toTimezone = $this->storageTimezone;
        }
        $date = $this->createDate($date);

        $date->timezone = $toTimezone;

        return $date;
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
        return $this->convertCollection($collection, $columns, ['direction' => 'to', 'format' => $format, 'timezone' => $fromTimezone]);
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
        return $this->convertCollection($collection, $columns, ['direction' => 'from', 'format' => $format, 'timezone' => $toTimezone]);
    }

    /**
     * Converts $dates or specified columns
     * throughout a collection.
     *
     * @param $collection
     * @param $columns
     * @param array $properties
     * @return Collection
     */
    protected function convertCollection($collection, $columns, array $properties): Collection
    {
        if ($collection instanceof Collection) {
            if (isset($properties['timezone']) && $properties['timezone'] === null) {
                $properties['timezone'] = $this->displayTimezone;
            }
            if (\is_array($properties['format']) && \count($properties['format']) !== 2) {
                throw new \InvalidArgumentException('Argument 3 $format should contain format and locale when specified as an array.');
            }
            $properties['columns'] = $columns;

            return $collection->map(function ($item) use ($properties) {
                if ($item instanceof Model) {
                    $properties['columns'] = array_merge($properties['columns'], $item->getDates());
                }
                foreach ($properties['columns'] as $column) {
                    if (\is_array($item)) {
                        $item[$column] = (string)$properties['direction'] === 'from' ? $this->convertFromStorage($item[$column], $properties['timezone']) : $this->convertToStorage($item[$column], $properties['timezone']);
                        if ($item[$column] instanceof TimezoneDate) {
                            if ($properties['format'] !== null) {
                                if (\is_array($properties['format'])) {
                                    $item[$column] = $item[$column]->formatToLocale($properties['format'][0], $properties['format'][1]);
                                } else {
                                    $item[$column] = $item[$column]->format($properties['format']);
                                }
                            } else {
                                $item[$column] = $item[$column]->formatDefault();
                            }
                        }
                    } else {
                        $item->$column = (string)$properties['direction'] === 'from' ? $this->convertFromStorage($item->$column, $properties['timezone']) : $this->convertToStorage($item->$column, $properties['timezone']);
                        if ($item->$column instanceof TimezoneDate) {
                            if ($properties['format'] !== null) {
                                if (\is_array($properties['format'])) {
                                    $item->$column = $item->$column->formatToLocale($properties['format'][0], $properties['format'][1]);
                                } else {
                                    $item->$column = $item->$column->format($properties['format']);
                                }
                            } else {
                                $item->$column = $item->$column->formatDefault();
                            }
                        }
                    }
                }

                return $item;
            });
        }

        throw new \InvalidArgumentException('A valid collection must be specified.');
    }

    /**
     * Create a Date object.
     *
     * @param null $date
     * @param null $timezone
     *
     * @return TimezoneDate
     */
    protected function createDate($date = null, $timezone = null): TimezoneDate
    {
        if (!$timezone) {
            $timezone = $this->storageTimezone;
        }
        if (\is_int($date)) {
            $date = date('Y-m-d H:i:s', $date);
        }
        if ($date instanceof Carbon) {
            $date = $date->format('Y-m-d H:i:s');
        }
        if ($this->parseUK) {
            $date = $this->formatUKDate($date);
        }
        if (!$this->isTimestamp($date)) {
            $timezone = $this->storageTimezone;
        }

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
                    $timezones[$timezone] = '('.$this->formatGmtOffset($offset).') '.$this->formatTimezoneName($timezone);
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
     *
     * @return string
     */
    protected function formatGmtOffset($offset): string
    {
        $hours = (int) ($offset / 3600);
        $minutes = abs((int) ($offset % 3600 / 60));

        return 'GMT'.($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    /**
     * Format timezone name.
     *
     * @param $name
     *
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
     *
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
     *
     * @return bool
     */
    protected function isTimestamp($date): bool
    {
        return strpos($date, ':') ? true : false;
    }
}
