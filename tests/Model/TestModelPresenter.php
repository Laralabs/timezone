<?php

namespace Laralabs\Timezone\Tests\Model;

use Illuminate\Database\Eloquent\Model;
use Laralabs\Timezone\Traits\HasTimezonePresenter;

class TestModelPresenter extends Model
{
    use HasTimezonePresenter;

    protected $table = 'test_models';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'timestamp',
        'datetime',
        'date',
    ];

    protected $dates = [
        'timestamp',
    ];

    protected $timezoneDates = [
        'datetime'  => 'd/m/Y H:i:s',
        'timestamp' => ['l j F Y H:i:s', 'nl'],
        'date'      => 'd/m/Y',
        'time'      => 'H:i:s',
    ];
}
