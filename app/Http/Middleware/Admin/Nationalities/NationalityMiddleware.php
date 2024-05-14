<?php

namespace App\Http\Middleware\Admin\Nationalities;

use App\Extenders\BaseMiddleware as Middleware;

class NationalityMiddleware extends Middleware
{
    public function __construct() {
        $this->permissions = ['admin.nationalities.crud'];
    }
}
