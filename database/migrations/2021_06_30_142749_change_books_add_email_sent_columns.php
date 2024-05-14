<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBooksAddEmailSentColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            /* Solely for masungi generated emails */
                /* Sent on the 3rd day of banking after approval */
                $table->datetime('first_trail_request_reminder_email_sent_at')->nullable()->index();
                /* Sent 4 banking days before visit */
                $table->datetime('second_trail_request_reminder_email_sent_at')->nullable()->index();
                $table->boolean('thank_you_email_sent')->default(0)->index();
                $table->boolean('remaining_balance_email_sent')->default(0)->index();
                $table->boolean('lapsed_payment_email_sent')->default(0)->index();
                $table->boolean('expired_visit_request_email_sent')->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('trail_request_reminder_email_sent');
                $table->dropColumn('trail_request_reminder_email_sent_at');
                $table->dropColumn('thank_you_email_sent');
                $table->dropColumn('remaining_balance_email_sent');
                $table->dropColumn('lapsed_payment_email_sent');
                $table->dropColumn('expired_visit_request_email_sent');
        });
    }
}
