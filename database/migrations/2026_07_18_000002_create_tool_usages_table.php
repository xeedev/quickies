<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45)->nullable()->index();
            $table->string('fingerprint')->nullable()->index();
            $table->string('tool_slug')->index();
            $table->date('used_on')->index();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['ip', 'used_on']);
            $table->index(['user_id', 'used_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_usages');
    }
};
