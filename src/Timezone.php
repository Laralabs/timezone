<?php

namespace Laralabs\Timezone;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;
use Laralabs\Timezone\Interfaces\TimezoneInterface;

class Timezone implements TimezoneInterface
{
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
        $this->storageTimezone = config('app.timezone');
        $this->displayTimezone = session()->has('timezone') ? session()->get('timezone') : config('timezone.timezone');
        $this->defaultFormat = config('timezone.format');
        $this->parseUK = config('timezone.parse_uk_dates');
    }

    /**
     * Convert timestamp from display to storage timezone.
     *
     * @param null $date
     * @param null $fromTimezone
     *
     * @return TimezoneDate|null
     */
    public function toStorage($date = null, $fromTimezone = null): ?TimezoneDate
    {
        if (!$fromTimezone) {
            $fromTimezone = $this->displayTimezone;
        }
        $date = $this->createDate($date, $fromTimezone);

        $date->timezone($this->storageTimezone);

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
    public function fromStorage($date = null, $toTimezone = null): ?TimezoneDate
    {
        if (!$toTimezone) {
            $toTimezone = $this->displayTimezone;
        }
        if (!\is_int($date) && !$this->isTimestamp($date)) {
            $toTimezone = $this->storageTimezone;
        }
        $date = $this->createDate($date);

        $date->timezone($toTimezone);

        return $date;
    }

    /**
     * Create a Date object.
     *
     * @param null $date
     * @param null $timezone
     *
     * @return TimezoneDate
     */
    protected function createDate($date = null, $timezone = null): Date
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

        return TimezoneDate::parse($date, $timezone);
    }

    /**
     * Get's the current timezone from
     * the session.
     *
     * @return mixed|null
     */
    public function getCurrentTimezone()
    {
        return $this->displayTimezone;
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
    public function isTimestamp($date): bool
    {
        return strpos($date, ':') ? true : false;
    }
}
