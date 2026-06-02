<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('document_id')->constrained('documents')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->tinyInteger('signing_order')->default(1);
            $table->string('token', 64)->unique();
            $table->enum('status', ['pending', 'viewed', 'signed', 'rejected', 'expired'])
                  ->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->longText('signature_data')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('invitation_sent_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signers');
    }
};
