<?php

namespace App\Http\Middleware\Admin\Pages;

use App\Extenders\BaseMiddleware as Middleware;

class PageItemMiddleware extends Middleware
{
    public function __construct() {
        $this->permissions = ['admin.page-items.crud'];
    }
}
