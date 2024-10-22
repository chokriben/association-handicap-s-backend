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
        Schema::create('type_organisations_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_organisation_id');
            $table->foreign('type_organisation_id')
                ->references('id')
                ->on('type_organisations')
                ->cascadeOnDelete();

            $table->string('locale')->index();

            // Ajout d'un nom d'index plus court pour éviter l'erreur de longueur
            $table->unique(['type_organisation_id', 'locale'], 'type_org_locale_unique');

            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_organisations_translations');
    }
};
