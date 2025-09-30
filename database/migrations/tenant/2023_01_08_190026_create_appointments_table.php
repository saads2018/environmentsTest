<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('patient_id');
            $table->uuid('physician_id');

            $table->datetime('start_time');
            $table->datetime('finish_time');

            $table->string('type'); //appointment, telehealth, personal
            $table->string('visit_type'); // initial, follow-up etc
            $table->longText('notes')->nullable();

            $table->foreign('physician_id')->references('id')->on('physician_profiles')
                ->onDelete('cascade');

            $table->foreign('patient_id')->references('id')->on('patient_profiles')
                ->onDelete('cascade');

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
        Schema::dropIfExists('appointments');
    }
};
