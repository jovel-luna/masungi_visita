<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeGeneratedEmailsTableAddExperienceIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('generated_emails', function (Blueprint $table) {
            $table->integer('experience_id')->ungsigned()->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('generated_emails', function (Blueprint $table) {
            $table->dropColumn('experience_id');
        });
    }
}
