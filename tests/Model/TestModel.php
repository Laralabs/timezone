<?php

namespace Laralabs\Timezone\Tests\Model;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'test_models';

    protected $timestamps = false;

    protected $fillable = [
        'name',
        'timestamp',
        'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'timestamp'
    ];
}
