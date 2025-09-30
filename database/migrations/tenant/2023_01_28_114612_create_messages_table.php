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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id');

            $table->uuid('patient_id');
            $table->uuid('from_id');

            $table->json('body');

            $table->foreign('patient_id')->references('id')->on('patient_profiles')
                ->onDelete('cascade');

            $table->primary(['id', 'patient_id', 'from_id']);

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
        Schema::dropIfExists('messages');
    }
};
