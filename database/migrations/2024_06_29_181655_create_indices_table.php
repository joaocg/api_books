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
        Schema::create('indices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('livro_id')->index();
            $table->unsignedBigInteger('indice_pai_id')->index()->nullable();
            $table->string('titulo')->index();
            $table->integer('pagina')->index();
            $table->timestamps();
            $table->foreign('livro_id')->references('id')->on('livros');
            $table->foreign('indice_pai_id')->references('id')->on('indices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indices');
    }
};
