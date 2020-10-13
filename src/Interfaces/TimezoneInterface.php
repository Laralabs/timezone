<?php

namespace Laralabs\Timezone\Interfaces;

use Laralabs\Timezone\TimezoneDate;

interface TimezoneInterface
{
    /**
     * @param null $date
     * @param null $fromTimezone
     *
     * @return TimezoneDate|null
     */
    public function toStorage($date = null, $fromTimezone = null): ?TimezoneDate;

    /**
     * @param null $date
     * @param null $toTimezone
     *
     * @return TimezoneDate|null
     */
    public function fromStorage($date = null, $toTimezone = null): ?TimezoneDate;

    /**
     * @return string|null
     */
    public function getCurrentTimezone(): ?string;

    /**
     * @return array
     */
    public function getTimezones(): array;

    /**
     * @param $value
     *
     * @return bool
     */
    public function isTimestamp($value): bool;

    /**
     * @param $value
     *
     * @return bool
     */
    public function isTime($value): bool;

    /**
     * @param $value
     *
     * @return bool
     */
    public function isDate($value): bool;
}
