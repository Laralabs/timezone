<?php

namespace Laralabs\Timezone\Facades;

use Laralabs\Timezone\Interfaces\TimezoneInterface;
use Laralabs\Timezone\TimezoneDate;

class TimezoneFacade implements TimezoneInterface
{
    /**
     * @param null $date
     * @param null $fromTimezone
     *
     * @return TimezoneDate|null
     */
    public function toStorage($date = null, $fromTimezone = null): ?TimezoneDate
    {
        return app('timezone')->toStorage($date, $fromTimezone);
    }

    /**
     * @param null $date
     * @param null $toTimezone
     *
     * @return TimezoneDate|null
     */
    public function fromStorage($date = null, $toTimezone = null): ?TimezoneDate
    {
        return app('timezone')->fromStorage($date, $toTimezone);
    }

    /**
     * @return string|null
     */
    public function getCurrentTimezone(): ?string
    {
        return app('timezone')->getCurrentTimezone();
    }

    /**
     * @return array
     */
    public function getTimezones(): array
    {
        return app('timezone')->getTimezones();
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isTimestamp($value): bool
    {
        return app('timezone')->isTimestamp($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isTime($value): bool
    {
        return app('timezone')->isTime($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isDate($value): bool
    {
        return app('timezone')->isDate($value);
    }
}
