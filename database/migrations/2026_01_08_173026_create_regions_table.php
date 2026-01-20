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
        Schema::create('regions', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('rgncode')->unique()->nullable();
            $table->string('rgnnom')->unique();
            $table->string('rgncheflieu')->unique();
            $table->enum('rgnactive', ["non", "oui"])->default("oui");
            $table->string('rgncommentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
