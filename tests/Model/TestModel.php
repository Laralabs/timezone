<?php

namespace Laralabs\Timezone\Tests\Model;

use Illuminate\Database\Eloquent\Model;
use Laralabs\Timezone\Traits\HasTimezonePresenter;

class TestModel extends Model
{
    use HasTimezonePresenter;

    protected $table = 'test_models';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'timestamp',
        'datetime',
        'date',
        'time',
    ];

    protected $dates = [
        'timestamp',
    ];
}
