<?php

namespace Laralabs\Timezone;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Date\Date;
use Laralabs\Timezone\Exceptions\TimezoneException;
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

    public function __construct()
    {
        $this->storageTimezone = config('app.timezone');
        $this->displayTimezone = session()->has('timezone') ? session()->get('timezone') : config('timezone.timezone');
        $this->defaultFormat = config('timezone.format');
    }

    /**
     * @param null $date
     * @param null $fromTimezone
     *
     * @return TimezoneDate|null
     * @throws TimezoneException
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
     * @param null $date
     * @param null $toTimezone
     *
     * @return TimezoneDate|null
     * @throws TimezoneException
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
     * @param null $date
     * @param null $timezone
     *
     * @return TimezoneDate
     * @throws TimezoneException
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
        if (!$this->isTimestamp($date)) {
            $timezone = $this->storageTimezone;
        }

        try {
            return TimezoneDate::parse($this->sanitizeDate($date), $timezone);
        } catch (\Exception $e) {
            throw new TimezoneException('Error parsing time string, the format of ('.$date.') is invalid');
        }
    }

    /**
     * @return string|null
     */
    public function getCurrentTimezone():? string
    {
        return $this->displayTimezone;
    }

    /**
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
     * @param $offset
     *
     * @return string
     */
    private function formatGmtOffset($offset): string
    {
        $hours = (int) ($offset / 3600);
        $minutes = abs((int) ($offset % 3600 / 60));

        return 'GMT'.($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    private function formatTimezoneName($name): string
    {
        return str_replace(['/', '_', 'St '], [', ', ' ', 'St. '], $name);
    }

    /**
     * @param $date
     *
     * @return string
     */
    protected function sanitizeDate($date): string
    {
        return str_replace('/', '-', $date);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isTimestamp($value): bool
    {
        if (strpos($value, ':')) {
            return (strpos($value, '-') || strpos($value, '/')) || \strlen($value) > 8;
        }

        return false;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isTime($value): bool
    {
        if (strpos($value, ':')) {
            return !(strpos($value, '-') || strpos($value, '/')) && \strlen($value) <= 9;
        }

        return false;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isDate($value): bool
    {
        if (strpos($value, '-') || strpos($value, '/')) {
            return !strpos($value, ':') && \strlen($value) === 10;
        }

        return false;
    }
}
