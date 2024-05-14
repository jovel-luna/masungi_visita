<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnsInVisitorTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_types', function (Blueprint $table) {
            $table->dropColumn('weekend_fee');
            $table->dropColumn('weekday_fee');
            $table->dropColumn('daytour_fee');
            $table->dropColumn('overnight_fee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitor_types', function (Blueprint $table) {
            //
        });
    }
}
