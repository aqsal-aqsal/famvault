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
        Schema::create('utility_records', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // Electricity, Water, Insurance, Internet, Property Tax
            $table->string('account_number');
            $table->string('account_holder_name')->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_records');
    }
};
