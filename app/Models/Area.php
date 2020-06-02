<?php

namespace App\Models;

use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    //
    use ModelTree;

    protected $table = 'areas';

    protected $titleColumn = 'name';
    protected $parentColumn = 'pid';


    // 返回空值即可禁用 order 字段
    public function getOrderColumn()
    {
        return null;
    }
}
