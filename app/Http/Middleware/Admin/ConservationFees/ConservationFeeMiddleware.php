<?php

namespace App\Http\Middleware\Admin\ConservationFees;

use App\Extenders\BaseMiddleware as Middleware;

class ConservationFeeMiddleware extends Middleware
{
    public function __construct() {
        $this->permissions = ['admin.conservation-fees.crud'];
    }
}
