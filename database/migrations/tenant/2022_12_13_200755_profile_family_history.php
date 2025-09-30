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
        Schema::create('profile_familyhistory', function (Blueprint $table) {
            
            $table->uuid('profile_id');
            $table->uuid('history_id');

            $table->foreign('profile_id')->references('id')->on('patient_profiles')
                ->onDelete('cascade');

            $table->foreign('history_id')->references('id')->on('family_histories')
                ->onDelete('cascade');

            $table->primary(['profile_id', 'history_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_familyhistory');
    }
};
