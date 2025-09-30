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
        Schema::create('physician_messages', function (Blueprint $table) {
            $table->uuid('id');

            $table->uuid('conversation_id');

            $table->uuid('from_id');

            $table->json('body');

            $table->foreign('conversation_id')->references('id')->on('conversations')
                ->onDelete('cascade');

                $table->foreign('from_id')->references('id')->on('users')
                ->onDelete('cascade');

            $table->primary(['id', 'from_id']);

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
        //
    }
};
