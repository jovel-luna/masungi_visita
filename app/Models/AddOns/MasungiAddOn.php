<?php

namespace App\Models\AddOns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasungiAddOn extends Model
{
	use SoftDeletes;
	protected $connection = 'masungi';
	protected $table = 'add_ons';
}
