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
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('usuarios_ou_dn')->nullable()->after('ou_dn');
            $table->string('grupos_ou_dn')->nullable()->after('usuarios_ou_dn');
            $table->string('computadores_ou_dn')->nullable()->after('grupos_ou_dn');
            $table->string('servidores_ou_dn')->nullable()->after('computadores_ou_dn');
            $table->string('desativados_ou_dn')->nullable()->after('servidores_ou_dn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'usuarios_ou_dn',
                'grupos_ou_dn',
                'computadores_ou_dn',
                'servidores_ou_dn',
                'desativados_ou_dn',
            ]);
        });
    }
};
