<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('acomptes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->unsignedBigInteger('product_id'); // Enlever nullable pour rendre le champ obligatoire.
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); // Modifier également la directive onDelete.
            $table->decimal('montant', 8, 2);
            $table->dateTime('date');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acomptes');
    }
};
