<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnCapacitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capacities', function (Blueprint $table) {
            $table->integer('online')->default(0)->change();
            $table->integer('walk_in')->default(0)->change();
            $table->integer('mgt_lgu')->default(0)->change();
            $table->integer('agency')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
