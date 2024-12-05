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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->unsigned()->nullable();
            $table->string('file_extension', 10)->nullable();
            $table->string('documentable_type')->nullable();
            $table->unsignedBigInteger('documentable_id')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('file_type');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
