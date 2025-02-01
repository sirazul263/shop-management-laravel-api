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
        Schema::create('stores', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name'); // Store name
            $table->text('address')->nullable(); // Store address (nullable)
            $table->string('phone')->nullable(); // Phone number (nullable)
            $table->string('image')->nullable(); // Store image URL or path (nullable)
            $table->text('description')->nullable(); // Store description (nullable)
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE'); // Status
            $table->foreignId('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps(); // Created_at & updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
