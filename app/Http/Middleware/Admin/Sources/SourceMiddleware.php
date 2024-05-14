<?php

namespace App\Http\Middleware\Admin\Sources;

use App\Extenders\BaseMiddleware as Middleware;

class SourceMiddleware extends Middleware
{
    public function __construct() {
        $this->permissions = ['admin.sources.crud'];
    }
}
