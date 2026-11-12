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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->date('death_date')->nullable();
            $table->string('phone', 30)->nullable();
            
            // Self-referencing pedigree relationships
            $table->foreignId('father_id')->nullable()->constrained('family_members')->nullOnDelete();
            $table->foreignId('mother_id')->nullable()->constrained('family_members')->nullOnDelete();
            $table->foreignId('spouse_id')->nullable()->constrained('family_members')->nullOnDelete();

            $table->string('avatar_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
