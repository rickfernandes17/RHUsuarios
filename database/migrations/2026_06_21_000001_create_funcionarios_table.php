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
        Schema::create('funcionarios', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $blueprint->string('nome');
            $blueprint->string('sobrenome');
            $blueprint->string('cpf')->unique();
            $blueprint->string('cargo');
            $blueprint->string('email_corporativo')->unique();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionarios');
    }
};
