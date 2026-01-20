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
        Schema::create('sessionremises', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->date('serdatedebut');
            $table->date('serdatefin');
            $table->enum('seractive', ["non", "oui"])->default("non");
            $table->string('sercommentaire')->nullable();
            $table->timestamps();
            // One-to-many : Anneeacademique 1..1 <==> 0..* Sessionremise
            $table->unsignedBigInteger('anneeacademique_id');
            $table->foreign('anneeacademique_id')->references('id')->on('anneeacademiques');
            // One-to-many : Typerepondant 1..1 <==> 0..* Sessionremise
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
        Schema::dropIfExists('sessionremises');
    }
};
