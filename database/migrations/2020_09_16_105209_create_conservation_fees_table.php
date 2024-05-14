<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConservationFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conservation_fees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('experience_id')->unsigned()->index();
            $table->integer('visitor_type_id')->unsigned()->index()->nullable();
            $table->integer('special_fee_id')->unsigned()->index()->nullable();
            $table->decimal('weekday_fee', 9, 2)->nullable();
            $table->decimal('weekend_fee', 9, 2)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conservation_fees');
    }
}
