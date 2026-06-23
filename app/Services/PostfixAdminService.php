<?php

namespace App\Services;

use App\Models\PostfixAlias;
use App\Models\PostfixDomain;
use App\Models\PostfixMailbox;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostfixAdminService
{
    /**
     * Criptografa a senha de acordo com o esquema configurado.
     */
    public function hashPassword(string $password): string
    {
        $scheme = env('POSTFIX_PASSWORD_SCHEME', 'md5crypt');
        $prefix = env('POSTFIX_PASSWORD_PREFIX', '');

        if ($scheme === 'md5crypt') {
            // MD5-Crypt: gera hash no formato $1$SALT$HASH — compatível com Dovecot
            $salt = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
            $hash = crypt($password, '$1$' . $salt . '$');
        } elseif ($scheme === 'sha512crypt') {
            // SHA512-Crypt: gera hash no formato $6$SALT$HASH — mais seguro
            $salt = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 16);
            $hash = crypt($password, '$6$' . $salt . '$');
        } elseif ($scheme === 'bcrypt') {
            $hash = password_hash($password, PASSWORD_BCRYPT);
        } elseif ($scheme === 'md5') {
            // MD5 puro (sem salt) — evitar em produção
            $hash = md5($password);
        } else {
            // Fallback: armazena em texto plano (não recomendado)
            $hash = $password;
        }

        return $prefix . $hash;
    }

    /**
     * Cria uma nova conta de e-mail (Mailbox e Alias) no PostfixAdmin.
     */
    public function createMailAccount(string $nomeCompleto, string $username, string $password, string $domain): bool
    {
        // Certificar-se de que o e-mail está em minúsculas
        $username = strtolower(trim($username));
        $domain = strtolower(trim($domain));

        try {
            DB::connection('mysql_postfix')->beginTransaction();

            // 1. Garantir que o domínio existe na tabela `domain` do PostfixAdmin.
            // Isso evita falhas de chave estrangeira em ambientes locais ou no servidor.
            $postfixDomain = PostfixDomain::find($domain);
            if (!$postfixDomain) {
                PostfixDomain::create([
                    'domain' => $domain,
                    'description' => 'Criado automaticamente pelo RH do Laravel',
                    'active' => 1,
                    'created' => now(),
                    'modified' => now(),
                ]);
                Log::info("Domínio {$domain} adicionado ao PostfixAdmin.");
            }

            // Verificar se o e-mail (username) já existe na mailbox
            if (PostfixMailbox::find($username)) {
                throw new \Exception("A conta de e-mail {$username} já existe no PostfixAdmin.");
            }

            // 2. Criar a Mailbox
            // O maildir é o caminho no servidor de e-mail, por padrão: dominio/username/
            // Ex: empresa.com/usuario/
            $localPart = explode('@', $username)[0];
            $maildir = $domain . '/' . $localPart . '/';
            $quotaBytes = env('POSTFIX_MAILBOX_QUOTA', 1073741824); // Padrão 1GB

            PostfixMailbox::create([
                'username' => $username,
                'password' => $this->hashPassword($password),
                'name' => $nomeCompleto,
                'maildir' => $maildir,
                'quota' => $quotaBytes,
                'domain' => $domain,
                'active' => 1,
                'created' => now(),
                'modified' => now(),
            ]);

            // 3. Criar o Alias correspondente (mapeia o email para ele mesmo)
            // No PostfixAdmin, para poder receber e-mails na caixa, é necessário ter um registro de alias
            // onde `address` = email_completo e `goto` = email_completo.
            $alias = PostfixAlias::find($username);
            if ($alias) {
                // Se já existe alias (raro sem mailbox), atualiza o destino
                $alias->goto = $username;
                $alias->active = 1;
                $alias->modified = now();
                $alias->save();
            } else {
                PostfixAlias::create([
                    'address' => $username,
                    'goto' => $username,
                    'domain' => $domain,
                    'active' => 1,
                    'created' => now(),
                    'modified' => now(),
                ]);
            }

            DB::connection('mysql_postfix')->commit();
            Log::info("E-mail {$username} criado com sucesso no PostfixAdmin.");
            return true;
        } catch (\Exception $exception) {
            DB::connection('mysql_postfix')->rollBack();
            Log::error("Erro ao criar e-mail no PostfixAdmin: " . $exception->getMessage());
            throw $exception;
        }
    }
}
