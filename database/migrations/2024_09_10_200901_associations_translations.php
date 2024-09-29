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
        Schema::create('association_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->unique(['association_id', 'locale']);
            $table->unsignedBigInteger('association_id');
            $table->foreign('association_id')->references('id')->on('associations')->cascadeOnDelete();
            $table->string('adresse');
            $table->string('adresse_reception')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['association_id', 'locale', 'name'], 'association_locale_name_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('associations_translations');
    }
};
