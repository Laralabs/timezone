<?php

namespace Laralabs\Timezone\Presenter;

use Illuminate\Database\Eloquent\Model;

abstract class Presenter
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param $value
     *
     * @return false|int
     */
    protected function hasMicroseconds($value)
    {
        return preg_match('/[0-5][0-9]\.([0-9]){1,6}$/', $value);
    }
}
