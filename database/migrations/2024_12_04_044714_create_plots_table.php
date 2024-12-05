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
    { Schema::create('plots', function (Blueprint $table) {
        $table->id();
        $table->string('plot_number')->unique()->nullable();
        $table->decimal('length', 10, 2)->nullable();  // Add length field
        $table->decimal('width', 10, 2)->nullable();   // Add width field
        $table->decimal('size', 10, 2)->nullable();
        $table->string('size_unit')->nullable();
        $table->decimal('price', 12, 2)->nullable();
        $table->string('location')->nullable();
        $table->text('address')->nullable();
        $table->json('amenities')->nullable();
        $table->text('description')->nullable();
        $table->string('legal_status')->nullable();
        $table->enum('status', ['available', 'reserved', 'sold'])->default('available')->nullable();
        $table->json('coordinates')->nullable();
        $table->string('site_plan')->nullable();
        $table->string('title_deed')->nullable();
        $table->string('chief_letter')->nullable();
        $table->string('chief_name')->nullable();
        $table->json('legal_practitioners')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
