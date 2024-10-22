<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('organisation_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->unique(['organisation_id', 'locale']);
            $table->unsignedBigInteger('organisation_id');
            $table->foreign('organisation_id')->references('id')->on('organisations')->cascadeOnDelete();
            $table->string('adresse');
            $table->string('adresse_reception')->nullable();
            $table->string('name');
            $table->string('category')->nullable();
            $table->longText('description')->nullable();
            $table->unique(['organisation_id', 'locale', 'name'], 'organisation_locale_name_unique');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('organisation_translations');
    }
};
