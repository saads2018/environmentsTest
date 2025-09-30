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
        Schema::create('physician_patient', function (Blueprint $table) {

            $table->uuid('physician_id');
            $table->uuid('patient_id');

            $table->foreign('physician_id')->references('id')->on('physician_profiles')
                ->onDelete('cascade');

            $table->foreign('patient_id')->references('id')->on('patient_profiles')
                ->onDelete('cascade');

            $table->primary(['physician_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('physician_patient');
    }
};
