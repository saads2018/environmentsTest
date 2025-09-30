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
        Schema::create('profile_quizzes', function (Blueprint $table) {
            $table->uuid('profile_id');
            $table->uuid('quiz_id');

            $table->foreign('profile_id')->references('id')->on('patient_profiles')
                ->onDelete('cascade');

            $table->string("score");

            $table->primary(['profile_id', 'quiz_id']);
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
        Schema::dropIfExists('quiz_patient');
    }
};
