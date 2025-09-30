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
        Schema::create('questionaires', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('patient_id')->nullable();

            $table->foreign('patient_id')->references('id')->on('patient_profiles')
                ->onDelete('set null');

            $table->json('answer_data');
            $table->json('lifestyle_data');
            $table->string('patient_report')->nullable();
            $table->string('physician_report')->nullable();
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
        Schema::dropIfExists('questionaires');
    }
};
