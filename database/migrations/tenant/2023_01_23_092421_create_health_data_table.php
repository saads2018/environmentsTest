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
        Schema::create('health_data', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('patient_id');

            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('bmi')->nullable();
            $table->string('bodyfat')->nullable();
            $table->string('bp')->nullable();
            $table->string('resting_hr')->nullable();

            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patient_profiles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_data');
    }
};
