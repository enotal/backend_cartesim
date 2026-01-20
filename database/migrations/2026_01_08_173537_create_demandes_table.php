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
        Schema::create('demandes', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('dmdcode')->unique()->nullable();
            $table->date('dmddate');
            $table->string('dmdcommentaire')->nullable();
            $table->timestamps();
            // One-to-many : Repondant 1..1 <==> 0..* Demande
            $table->unsignedBigInteger('repondant_id');
            $table->foreign('repondant_id')->references('id')->on('repondants');
            // One-to-many : Sessiondemande 1..1 <==> 0..* Demande
            $table->unsignedBigInteger('sessiondemande_id');
            $table->foreign('sessiondemande_id')->references('id')->on('sessiondemandes');
            // One-to-many : Sessionremise 1..1 <==> 0..* Demande
            $table->unsignedBigInteger('sessionremise_id')->nullable();
            $table->foreign('sessionremise_id')->references('id')->on('sessionremises');
            // One-to-many : Site 1..1 <==> 0..* Demande
            $table->unsignedBigInteger('site_id')->nullable();
            $table->foreign('site_id')->references('id')->on('sites');
            // One-to-many : User 0..1 <==> 0..* Demande
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            // 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
