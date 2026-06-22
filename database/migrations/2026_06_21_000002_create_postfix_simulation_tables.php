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
        // Se a conexão mysql_postfix estiver configurada para apontar para a simulação, criamos as tabelas.
        // Usamos Schema::connection('mysql_postfix') para garantir que rode no banco do PostfixAdmin.
        try {
            Schema::connection('mysql_postfix')->create('domain', function (Blueprint $blueprint) {
                $blueprint->string('domain')->primary();
                $blueprint->string('description')->nullable();
                $blueprint->tinyInteger('active')->default(1);
                $blueprint->dateTime('created')->useCurrent();
                $blueprint->dateTime('modified')->useCurrent();
            });

            Schema::connection('mysql_postfix')->create('mailbox', function (Blueprint $blueprint) {
                $blueprint->string('username')->primary(); // email completo (ex: user@domain.com)
                $blueprint->string('password');
                $blueprint->string('name');
                $blueprint->string('maildir');
                $blueprint->bigInteger('quota')->default(0);
                $blueprint->string('domain');
                $blueprint->tinyInteger('active')->default(1);
                $blueprint->dateTime('created')->useCurrent();
                $blueprint->dateTime('modified')->useCurrent();

                $blueprint->foreign('domain')->references('domain')->on('domain')->onDelete('cascade');
            });

            Schema::connection('mysql_postfix')->create('alias', function (Blueprint $blueprint) {
                $blueprint->string('address')->primary(); // email completo (ex: user@domain.com)
                $blueprint->text('goto'); // para onde redireciona (ex: user@domain.com)
                $blueprint->string('domain');
                $blueprint->tinyInteger('active')->default(1);
                $blueprint->dateTime('created')->useCurrent();
                $blueprint->dateTime('modified')->useCurrent();

                $blueprint->foreign('domain')->references('domain')->on('domain')->onDelete('cascade');
            });
        } catch (\Exception $exception) {
            // Caso o banco remoto ou de simulação não esteja disponível durante a execução local comum
            // (por exemplo, se o usuário já estiver apontando para o servidor remoto e não tiver permissões DDL),
            // registramos o log ou ignoramos para não quebrar as migrations do sistema de RH.
            logger()->warning('Não foi possível criar as tabelas de simulação do PostfixAdmin: ' . $exception->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::connection('mysql_postfix')->dropIfExists('alias');
            Schema::connection('mysql_postfix')->dropIfExists('mailbox');
            Schema::connection('mysql_postfix')->dropIfExists('domain');
        } catch (\Exception $exception) {
            logger()->warning('Erro ao remover tabelas de simulação do PostfixAdmin: ' . $exception->getMessage());
        }
    }
};
