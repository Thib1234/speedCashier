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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('cash', 8, 2); // Ajoutez une colonne pour le montant total
            $table->decimal('bancontact', 8, 2); // Ajoutez une colonne pour le montant total
            $table->decimal('credit_card', 8, 2); // Ajoutez une colonne pour le montant total
            $table->decimal('virement', 8, 2); // Ajoutez une colonne pour le montant total
            $table->decimal('total_amount', 8, 2); // Ajoutez une colonne pour le montant total
            $table->timestamps();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
