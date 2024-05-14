<?php

namespace App\Http\Middleware\Admin\AgeRanges;

use App\Extenders\BaseMiddleware as Middleware;

class AgeRangeMiddleware extends Middleware
{
    public function __construct() {
        $this->permissions = ['admin.age-ranges.crud'];
    }
}
