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
        Schema::create('clinical_notes', function (Blueprint $table) {
            $table->uuid('id');

            $table->uuid('patient_id');
            $table->uuid('appt_id');
            $table->uuid("health_data_id")->nullable();

            $table->string('time_in')->nullable();
            $table->string('time_out')->nullable();
            $table->longText("counselling")->nullable();
            $table->longText("discussed")->nullable();
            $table->string('next_appt')->nullable();
            $table->longText("homework")->nullable();
            $table->string("next_followup_physical")->nullable();
            $table->string("next_followup_labs")->nullable();


            $table->foreign('patient_id')->references('id')->on('patient_profiles')
                ->onDelete('cascade');
            $table->foreign('appt_id')->references('id')->on('appointments')
                ->onDelete('cascade');
            $table->foreign('health_data_id')->references('id')->on('health_data')
                ->onDelete('cascade');

            $table->timestamps();
            
            $table->primary(['id', 'patient_id']);

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
};
