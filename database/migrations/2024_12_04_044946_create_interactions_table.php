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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'inquiry',
                'viewing',
                'negotiation',
                'follow_up',
                'complaint',
                'documentation',
                'payment',
                'other'
            ]);
            $table->enum('status', [
                'pending',
                'in_progress',
                'completed',
                'requires_follow_up'
            ])->default('pending');
            $table->text('notes');
            $table->dateTime('interaction_date');
            $table->dateTime('follow_up_date')->nullable();
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent'
            ])->default('medium');
            $table->json('tags')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
