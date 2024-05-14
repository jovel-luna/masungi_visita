<?php

namespace App\Http\Middleware\Admin\Copywritings;

use App\Extenders\BaseMiddleware as Middleware;

class CopywritingMiddleware extends Middleware
{
    public function __construct() {
        $this->permissions = ['admin.copywritings.crud'];
    }
}
