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
        Schema::create('profile_medconditions', function (Blueprint $table) {
            
            $table->uuid('profile_id');
            $table->uuid('condition_id');

            $table->foreign('profile_id')->references('id')->on('patient_profiles')
                ->onDelete('cascade');

            $table->foreign('condition_id')->references('id')->on('medical_conditions')
                ->onDelete('cascade');

            $table->primary(['profile_id', 'condition_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_medconditions');
    }
};
