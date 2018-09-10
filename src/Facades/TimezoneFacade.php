<?php

namespace Laralabs\Timezone\Facades;

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
    public function toStorage($date = null, $fromTimezone = null): ?TimezoneDate
    {
        return app('timezone')->toStorage($date, $fromTimezone);
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
        return app('timezone')->fromStorage($date, $toTimezone);
    }

    /**
     * Get's the current timezone from
     * the session.
     *
     * @return mixed|null
     */
    public function getCurrentTimezone()
    {
        return app('timezone')->getCurrentTimezone();
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

    /**
     * Check if given date is a timestamp.
     *
     * @param $date
     *
     * @return bool
     */
    public function isTimestamp($date): bool
    {
        return app('timezone')->isTimestamp($date);
    }
}
