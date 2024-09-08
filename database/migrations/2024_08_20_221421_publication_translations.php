<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('publication_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->unique(['publication_id', 'locale']);
            $table->unsignedBigInteger('publication_id');
            $table->foreign('publication_id')->references('id')->on('publications')->cascadeOnDelete();
            $table->string('titre');
            $table->longText('contenu');

        });
    }

    public function down()
    {
        Schema::dropIfExists('publication_translations');
    }
};

