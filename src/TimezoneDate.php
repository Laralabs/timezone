<?php

namespace Laralabs\Timezone;

use Jenssegers\Date\Date;

class TimezoneDate extends Date
{
    /**
     * @var string
     */
    protected static $currentLocale;

    /**
     * Date constructor.
     *
     * @param null|string $time
     * @param null        $timezone
     */
    public function __construct(?string $time = null, $timezone = null)
    {
        parent::__construct($time, $timezone);

        static::$currentLocale = static::getLocale();
    }

    /**
     * Formats date to the specified locale.
     *
     * @param string $format
     * @param string $locale
     *
     * @return mixed|string
     */
    public function formatToLocale(string $format, string $locale)
    {
        static::setLocale($locale);

        $date = $this->format($format);

        static::setLocale(static::$currentLocale);

        return $date;
    }
}
