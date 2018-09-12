<?php

namespace Laralabs\Timezone\Presenter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;
use Laralabs\Timezone\Exceptions\TimezonePresenterException;
use Laralabs\Timezone\TimezoneDate;

class TimezonePresenter extends Presenter
{
    /**
     * @var array
     */
    protected $dates;

    /**
     * @var string|null
     */
    public $property;

    /**
     * @var \Illuminate\Config\Repository|string|null
     */
    protected $displayTimezone;

    /**
     * @var \Illuminate\Config\Repository|string|null
     */
    protected $displayFormat;

    /**
     * @var mixed
     */
    protected $locale;

    /**
     * @param Model $model
     * @param array $dates
     */
    public function __construct(Model $model, array $dates)
    {
        parent::__construct($model);

        $this->dates = $dates;
        $this->displayTimezone = session()->has('timezone') ? session()->get('timezone') : config('timezone.timezone');
        $this->displayFormat = config('timezone.format');
        $this->locale = config('timezone.session_locale') ? Session::get('locale') : App::getLocale();
        Date::setLocale($this->locale);
    }

    /**
     * @param $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        return array_key_exists($property, $this->dates);
    }

    /**
     * @param $property
     *
     * @throws TimezonePresenterException
     *
     * @return $this
     */
    public function __get($property)
    {
        if ($this->__isset($property)) {
            $this->property = $property;
            if (!empty($this->dates[$property])) {
                $this->displayFormat = $this->dates[$property];
                if (\is_array($this->displayFormat)) {
                    $this->locale = $this->displayFormat[1];
                    $this->displayFormat = $this->displayFormat[0];
                }
            }

            return $this;
        }

        throw new TimezonePresenterException('Property '.$property.' not found in '.\get_class($this->model).'::$timezoneDates');
    }

    /**
     * @param $property
     * @param $value
     *
     * @throws TimezonePresenterException
     *
     * @return Model
     */
    public function __set($property, $value)
    {
        if ($this->__isset($property)) {
            $this->property = $property;

            $converted = timezone()->toStorage($value, $this->displayTimezone);
            $this->model->$property = $this->formatDate($property, $value, $converted);

            return $this->model;
        }

        throw new TimezonePresenterException('Property '.$property.' not found in '.\get_class($this->model).'::$timezoneDates');
    }

    /**
     * @param string       $property
     * @param string       $original
     * @param TimezoneDate $converted
     *
     * @return string
     */
    private function formatDate(string $property, string $original, TimezoneDate $converted): string
    {
        if (timezone()->isTimestamp($original)) {
            return !$this->hasMicroseconds($original) ? $converted->format('Y-m-d H:i:s') : $converted->format('Y-m-d H:i:s.u');
        }
        if (timezone()->isTime($original)) {
            return !$this->hasMicroseconds($original) ? $converted->format('H:i:s') : $converted->format('H:i:s.u');
        }
        if (timezone()->isDate($original)) {
            return $converted->format('Y-m-d');
        }

        return $this->model->$property;
    }

    /**
     * @param \Illuminate\Config\Repository|string|null $format
     * @param \Illuminate\Config\Repository|string|null $locale
     * @param string|null                               $toTimezone
     *
     * @throws TimezonePresenterException
     *
     * @return string
     */
    public function display(string $format = null, string $locale = null, string $toTimezone = null): string
    {
        if ($this->property) {
            $property = $this->property;
            $format = $format ?? $this->displayFormat;
            $locale = $locale ?? $this->locale;
            $timezone = $toTimezone ?? $this->displayTimezone;

            return timezone()->fromStorage($this->model->$property, $timezone)->formatToLocale($format, $locale);
        }

        throw new TimezonePresenterException('Please specify a property before attempting to convert it');
    }
}
