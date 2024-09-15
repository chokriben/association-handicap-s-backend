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
        Schema::create('associations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_association_id')->nullable(); // Add this line for the foreign key
            $table->string('phone')->nullable();
            $table->string('phone_fax')->nullable(); // Utilisez un underscore (_) au lieu du tiret (-)
            $table->string('rip')->nullable();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('associations');
    }
};
