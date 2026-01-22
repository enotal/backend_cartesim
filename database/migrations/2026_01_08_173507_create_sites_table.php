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
        Schema::create('sites', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('sitlibelle')->unique();
            $table->string('sitcommentaire')->nullable();
            $table->enum('sitactive', ["non", "oui"])->default("non");
            $table->timestamps();
            // One-to-many : Province 1..1 <==> 0..* Site
            $table->unsignedBigInteger('province_id');
            $table->foreign('province_id')->references('id')->on('provinces');
            // 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
