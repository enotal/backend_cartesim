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
        Schema::create('typerepondants', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('tyrcode')->unique();
            $table->string('tyrlibelle')->unique();
            $table->enum('tyractive', ["non", "oui"])->default("non");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('typerepondants');
    }
};
