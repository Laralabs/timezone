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
    public function toStorage($date = null, $fromTimezone = null): ?TimezoneDate;

    /**
     * Convert timestamp from storage to display timezone.
     *
     * @param null $date
     * @param null $toTimezone
     *
     * @return TimezoneDate|null
     */
    public function fromStorage($date = null, $toTimezone = null): ?TimezoneDate;

    /**
     * Get's the current timezone from
     * the session.
     *
     * @return mixed|null
     */
    public function getCurrentTimezone();

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
