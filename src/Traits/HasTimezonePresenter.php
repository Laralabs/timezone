<?php

namespace Laralabs\Timezone\Traits;

use Laralabs\Timezone\Exceptions\TimezonePresenterException;
use Laralabs\Timezone\Presenter\TimezonePresenter;

trait HasTimezonePresenter
{
    protected $timezonePresenter;

    /**
     * @return TimezonePresenter
     * @throws TimezonePresenterException
     */
    public function timezone()
    {
        if (property_exists($this, 'timezoneDates')) {
            return $this->timezonePresenter = new TimezonePresenter($this, $this->timezoneDates);
        }

        throw new TimezonePresenterException('Property $timezoneDates is not set correctly in '.get_class($this));
    }
}