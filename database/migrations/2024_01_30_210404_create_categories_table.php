<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Crée une colonne "id" en tant que clé primaire auto-incrémentée
            $table->string('name');
            $table->string('color');
            // Ajoutez d'autres colonnes au besoin
            $table->timestamps(); // Ajoute automatiquement les colonnes "created_at" et "updated_at"
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
