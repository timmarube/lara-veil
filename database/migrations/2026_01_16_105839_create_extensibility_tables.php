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
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->unique();
            $table->string('namespace', 191)->unique();
            $table->string('version', 20);
            $table->enum('status', ['active', 'inactive', 'broken'])->default('inactive');
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->unique();
            $table->string('slug', 191)->unique();
            $table->foreignId('parent_id')->nullable()->constrained('themes')->onDelete('cascade');
            $table->boolean('is_active')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
        Schema::dropIfExists('plugins');
    }
};
