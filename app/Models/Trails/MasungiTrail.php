<?php

namespace App\Models\Trails;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class MasungiTrail extends Model
{
    use SoftDeletes;
    protected $connection = 'masungi';
    protected $table = 'trails';
}
