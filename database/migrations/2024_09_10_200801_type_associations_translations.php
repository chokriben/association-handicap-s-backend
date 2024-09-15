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
        Schema::create('type_associations_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_association_id'); // Add this line
            $table->foreign('type_association_id')->references('id')->on('type_associations')->cascadeOnDelete();
            $table->string('locale');
            $table->unique(['type_association_id', 'locale']);
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_associations_translations');
    }
};
