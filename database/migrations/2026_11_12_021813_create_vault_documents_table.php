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
        Schema::create('vault_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_member_id')->nullable()->constrained('family_members')->nullOnDelete();
            $table->string('title');
            $table->string('category'); // identity, vehicle, property, education, health, other
            $table->string('encrypted_file_path');
            $table->string('file_mime', 100);
            $table->unsignedBigInteger('file_size_bytes');
            $table->string('iv_salt'); // Base64 Initialization Vector & Salt
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vault_documents');
    }
};
