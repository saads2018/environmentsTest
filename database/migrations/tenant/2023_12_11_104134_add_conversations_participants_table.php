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
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->uuid('conversation_id');
            $table->uuid('user_id');

            $table->foreign('conversation_id')->references('id')->on('conversations')
                ->onDelete('cascade');

                $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');

            $table->primary(['conversation_id', 'user_id']);
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
