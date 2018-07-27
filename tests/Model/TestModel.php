<?php

namespace Laralabs\Timezone\Tests\Model;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'test_models';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'timestamp',
        'datetime',
    ];

    protected $dates = [
        'timestamp',
    ];
}
