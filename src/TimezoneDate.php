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
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $defaultFormat;

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
        $this->defaultFormat = config('timezone.format');
    }

    /**
     * Create a TimezoneDate instance from a string.
     *
     * @param  string $time
     * @param  string|DateTimeZone $timezone
     * @return TimezoneDate
     */
    public static function parse($time = null, $timezone = null)
    {
        if ($time instanceof Carbon) {
            return new static(
                $time->toDateTimeString(),
                $timezone ?: $time->getTimezone()
            );
        }

        if (!is_int($time)) {
            $time = static::translateTimeString($time);
        }

        return new static($time, $timezone);
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

    /**
     * Formats date to the default config format.
     *
     * @return mixed|string
     */
    public function formatDefault()
    {
        return $this->format($this->defaultFormat);
    }
}
