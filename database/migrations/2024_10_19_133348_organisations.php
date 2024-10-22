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
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('type_organisation_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_fax')->nullable(); // Utilisez un underscore (_) au lieu du tiret (-)
            $table->string('rip')->nullable();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('organisations');
    }
};
