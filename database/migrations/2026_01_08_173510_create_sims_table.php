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
        Schema::create('sims', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('simnumero')->unique();
            $table->string('simcode')->unique()->nullable();
            $table->date('simdateactivation')->nullable();
            $table->date('simdateremise')->nullable();
            $table->date('simdatesuspension')->nullable();
            $table->date('simdateretrait')->nullable();
            $table->string('simperdue', ["non","oui"])->default("non"); 
            $table->string('simdeclarationperte')->nullable(); 
            $table->string('simcommentaire')->nullable();
            $table->timestamps(); 
            // One-to-many : Anneeacademique 1..1 <==> 0..* Sim
            $table->unsignedBigInteger('anneeacademique_id');
            $table->foreign('anneeacademique_id')->references('id')->on('anneeacademiques');
            // One-to-many : Demande 0..1 <==> 0..1 Sim
            $table->unsignedBigInteger('demande_id')->nullable();
            $table->foreign('demande_id')->references('id')->on('demandes');
            // One-to-many : Province 0..1 <==> 0..* Sim
            $table->unsignedBigInteger('province_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');
            // One-to-many : Region 0..1 <==> 0..* Sim
            $table->unsignedBigInteger('region_id')->nullable();
            $table->foreign('region_id')->references('id')->on('regions');
            //  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sims');
    }
};
