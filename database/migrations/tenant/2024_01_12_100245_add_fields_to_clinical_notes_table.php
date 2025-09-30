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
        Schema::table('clinical_notes', function (Blueprint $table) {
            
            $table->boolean('include')->default(false);
            $table->boolean('age')->nullable();
            $table->boolean('height')->nullable();
            $table->boolean('weight')->nullable();
            $table->boolean('bmi')->nullable();

            $table->string('ibw')->nullable();
            $table->string('bmr')->nullable();
            $table->string('food_allergies')->nullable();
            $table->string('med_allergies')->nullable();
            $table->string('nutrition_rel_labs')->nullable();
            $table->string('nutrition_rel_meds')->nullable();

            $table->string('nutrition_rel_diag')->nullable();
            $table->string('diet_order')->nullable();
            $table->string('texture')->nullable();
            $table->string('complications')->nullable();
            $table->string('est_cal_per_day')->nullable();
            $table->string('est_protein_per_day')->nullable();
            $table->string('est_carbs_per_day')->nullable();
            $table->string('est_fat_per_day')->nullable();
            $table->string('est_fluid_per_day')->nullable();

            $table->string('interventions')->nullable();
            $table->string('plan')->nullable();
            $table->string('notes')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clinical_notes', function (Blueprint $table) {
            //
        });
    }
};
