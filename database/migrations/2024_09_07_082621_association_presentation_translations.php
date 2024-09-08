<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('association_presentation_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('association_presentation_id');
            $table->string('locale')->index();
            $table->unique(['association_presentation_id', 'locale']);
            $table->text('de_nous')->nullable(); // Section "De nous"
            $table->text('notre_vision')->nullable(); // Section "Notre vision"
            $table->text('notre_message')->nullable(); // Section "Notre message"
            $table->text('nos_objectifs')->nullable(); // Section "Nos objectifs"
            $table->text('de_nouvelles_valeurs')->nullable(); // Section "De nouvelles valeurs"
            $table->foreign('association_presentation_id')
                  ->references('id')->on('association_presentations')
                  ->onDelete('cascade');
                  $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('association_presentation_translations');
    }
};
