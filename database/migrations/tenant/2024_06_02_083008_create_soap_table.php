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
        Schema::create('soap', function (Blueprint $table) {

            $table->uuid('id');

            $table->uuid('clinical_note_id');


            //#Subjective
            $table->text('hpi')->nullable();
            $table->text('family_hx')->nullable();
            $table->text('past_medical_hx')->nullable();
            $table->text('social_hx')->nullable();

            $table->boolean('general')->nullable();
            $table->string('general_dd')->nullable();
            $table->text('general_comments')->nullable();


            $table->boolean('skin')->nullable();
            $table->string('skin_dd')->nullable();
            $table->text('skin_comments')->nullable();

            $table->boolean('heent')->nullable();
            $table->string('heent_dd')->nullable();
            $table->text('heent_comments')->nullable();

            $table->boolean('neck')->nullable();
            $table->string('neck_dd')->nullable();
            $table->text('neck_comments')->nullable();

            $table->boolean('cardio')->nullable();
            $table->string('cardio_dd')->nullable();
            $table->text('cardio_comments')->nullable();

            $table->boolean('respiratory')->nullable();
            $table->string('respiratory_dd')->nullable();
            $table->text('respiratory_comments')->nullable();


            $table->boolean('gi')->nullable();
            $table->string('gi_dd')->nullable();
            $table->text('gi_comments')->nullable();


            $table->boolean('urinary')->nullable();
            $table->string('urinary_dd')->nullable();
            $table->text('urinary_comments')->nullable();

            $table->boolean('periph_vasc')->nullable();
            $table->string('periph_vasc_dd')->nullable();
            $table->text('periph_vasc_comments')->nullable();


            $table->boolean('msk')->nullable();
            $table->string('msk_dd')->nullable();
            $table->text('msk_comments')->nullable();

            $table->boolean('neuro')->nullable();
            $table->string('neuro_dd')->nullable();
            $table->text('neuro_comments')->nullable();

            $table->boolean('endo')->nullable();
            $table->string('endo_dd')->nullable();
            $table->text('endo_comments')->nullable();


            $table->boolean('psychiatric')->nullable();
            $table->string('psychiatric_dd')->nullable();
            $table->text('psychiatric_comments')->nullable();

            //#Objective
            $table->boolean('general_wnl')->nullable();
            $table->string('general_wnl_dd')->nullable();
            $table->text('general_wnl_comments')->nullable();

            $table->boolean('heent_wnl')->nullable();
            $table->string('heent_wnl_dd')->nullable();
            $table->text('heent_wnl_comments')->nullable();

            $table->boolean('skin_wnl')->nullable();
            $table->string('skin_wnl_dd')->nullable();
            $table->text('skin_wnl_comments')->nullable();

            $table->boolean('neck_wnl')->nullable();
            $table->string('neck_wnl_dd')->nullable();
            $table->text('neck_wnl_comments')->nullable();

            $table->boolean('cardio_wnl')->nullable();
            $table->string('cardio_wnl_dd')->nullable();
            $table->text('cardio_wnl_comments')->nullable();

            $table->boolean('lungs_wnl')->nullable();
            $table->string('lungs_wnl_dd')->nullable();
            $table->text('lungs_wnl_comments')->nullable();

            $table->boolean('abdomen_wnl')->nullable();
            $table->string('abdomen_wnl_dd')->nullable();
            $table->text('abdomen_wnl_comments')->nullable();

            $table->boolean('msk_wnl')->nullable();
            $table->string('msk_wnl_dd')->nullable();
            $table->text('msk_wnl_comments')->nullable();

            $table->boolean('neuro_wnl')->nullable();
            $table->string('neuro_wnl_dd')->nullable();
            $table->text('neuro_wnl_comments')->nullable();

            $table->boolean('extremities_wnl')->nullable();
            $table->string('extremities_wnl_dd')->nullable();
            $table->text('extremities_wnl_comments')->nullable();


            //#Assesment
            $table->text('billing_icd10')->nullable();
            $table->text('problems')->nullable();
            $table->text('billing_icd9')->nullable();
            $table->text('problem_history')->nullable();
            $table->text('assesments')->nullable();

            //#Plan
              //Clinical info
            $table->string('lab')->nullable();
            $table->text('lab_comms')->nullable();

            $table->string('radiology')->nullable();
            $table->text('radiology_comms')->nullable();

            $table->string('pt_rec')->nullable();
            $table->text('pt_rec_comms')->nullable();

            $table->string('home_health')->nullable();
            $table->text('home_health_comms')->nullable();

            $table->string('referrals')->nullable();
            $table->text('referrals_comms')->nullable();

            $table->string('edu')->nullable();
            $table->text('edu_comms')->nullable();

            $table->string('diet')->nullable();
            $table->text('diet_comms')->nullable();

            $table->string('general_ins')->nullable();
            $table->text('general_ins_comms')->nullable();


            //#Medication

            $table->foreign('clinical_note_id')->references('id')->on('clinical_notes')
                ->onDelete('cascade');

            $table->timestamps();

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('soap');
    }
};
