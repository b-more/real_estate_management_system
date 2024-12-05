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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('id_type')->nullable(); // passport, national_id, etc.
            $table->string('id_number')->nullable();
            $table->string('occupation')->nullable();
            $table->string('company_name')->nullable();
            $table->enum('type', ['individual', 'corporate'])->default('individual')->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->nullable();
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->json('preferences')->nullable();
            $table->json('tags')->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->decimal('total_purchases', 12, 2)->default(0);
            $table->timestamp('last_purchase_date')->nullable();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
