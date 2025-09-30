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
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->date('dob')->nullable();
            $table->boolean('patient_confirmed')->default(false);
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->string('codes')->nullable();
            $table->string('race')->nullable();
            $table->integer('physicians')->default(0);
            $table->string('ethnicity')->nullable();
            $table->string('language')->nullable();
            $table->string('religion')->nullable();
            $table->longText('notes')->nullable();
            
            $table->json("contact_info")->nullable();
            $table->json("emergency_contact")->nullable();
            $table->json("insurance_info")->nullable();

            $table->json("meds")->nullable();
            $table->json("data")->nullable();

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
        Schema::dropIfExists('patient_profiles');
    }
};
