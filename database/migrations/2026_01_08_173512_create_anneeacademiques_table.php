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
        Schema::create('anneeacademiques', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('acacode')->unique();
            $table->date('acadatedebut')->unique(); 
            $table->date('acadatefin')->unique(); 
            $table->enum('acaactive', ["non", "oui"])->default("non");
            $table->string('acacommentaire')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anneeacademiques');
    }
};
