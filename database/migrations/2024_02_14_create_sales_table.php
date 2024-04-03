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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('datetime');
            $table->decimal('cash', 8, 2); // Ajoutez une colonne pour le montant total
            $table->decimal('bancontact', 8, 2); // Ajoutez une colonne pour le montant total
            $table->decimal('credit_card', 8, 2); // Ajoutez une colonne pour le montant total
            $table->decimal('virement', 8, 2); // Ajoutez une colonne pour le montant total
            $table->decimal('total_amount', 8, 2); // Ajoutez une colonne pour le montant total
            $table->decimal('montant_total_htva', 8, 2);
            $table->decimal('amount_tva', 6, 2);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
