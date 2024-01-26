<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MenuCity extends BaseModel
{
    protected $connection = 'common';

    public function menus(): HasOne
    {
        return $this->hasone(Menu::class, 'id', 'menu_id');
    }
}
