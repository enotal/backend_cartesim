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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('prvcode')->unique()->nullable();
            $table->string('prvnom')->unique();
            $table->string('prvcheflieu')->unique();
            $table->enum('prvactive', ["non", "oui"])->default("oui");
            $table->string('prvcommentaire')->nullable();
            $table->timestamps(); 
            // One-to-many : Region 1..1 <==> 0..* Province
            $table->unsignedBigInteger('region_id');
            $table->foreign('region_id')->references('id')->on('regions'); 
            // 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
