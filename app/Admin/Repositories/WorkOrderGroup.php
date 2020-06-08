<?php

namespace App\Admin\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use App\Models\WorkOrderGroup as WorkOrderGroupModel;

class WorkOrderGroup extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = WorkOrderGroupModel::class;
}
