<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

           // Suppression de `->after('id')`
           $table->unsignedBigInteger('admin_id')->nullable();

           // Ajout de la clé étrangère
           $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('nom_association');
            $table->enum('type_organisation', ['Centre', 'Association', 'Organisme']);
            $table->string('adresse')->nullable();
            $table->string('telephone');
            $table->enum('role', ['super_admin', 'administrateur', 'membre', 'visiteur']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('profile_photo')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
