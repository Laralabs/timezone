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
}