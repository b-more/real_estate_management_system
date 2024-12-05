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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('type')->nullable(); // Use string instead of enum for SQLite
            $table->string('status')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('receipt_number')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
