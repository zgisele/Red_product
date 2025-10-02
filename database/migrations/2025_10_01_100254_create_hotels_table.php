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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
             $table->string('name');          // nom de l'hôtel
            $table->string('location');      // ville ou adresse
            $table->integer('rooms');        // nombre de chambres
            $table->decimal('price', 8, 2);  // prix par nuit
            $table->string('image')->nullable(); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // relation avec User
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
