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
        Schema::create('repondants', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('repidentifiant')->unique();
            $table->enum('repsexe', ["Féminin", "Masculin"]);
            $table->string('repemail')->unique();
            $table->enum('repactive', ["non", "oui"])->default("non");
            $table->timestamps();
            // One-to-many : Typerepondant 1..1 <==> 0..* Repondant
            $table->unsignedBigInteger('typerepondant_id');
            $table->foreign('typerepondant_id')->references('id')->on('typerepondants');
            // 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repondants');
    }
};
